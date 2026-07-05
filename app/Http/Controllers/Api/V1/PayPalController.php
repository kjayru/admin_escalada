<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    private ?string $clientId;
    private ?string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function logContext(array $extra = []): array
    {
        return array_merge([
            'mode' => config('services.paypal.mode'),
            'currency' => config('services.paypal.currency', 'MXN'),
            'base_url' => $this->baseUrl,
            'frontend_url' => config('app.frontend_url'),
            'client_id_configured' => filled($this->clientId),
            'client_secret_configured' => filled($this->clientSecret),
        ], $extra);
    }

    private function maskedEmail(?string $email): ?string
    {
        if (!$email || !str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $visible = substr($name, 0, 2);

        return $visible . str_repeat('*', max(strlen($name) - 2, 1)) . '@' . $domain;
    }

    /**
     * Obtener token de acceso de PayPal
     */
    private function getAccessToken(): ?string
    {
        try {
            Log::info('PayPal: Solicitando access token', $this->logContext());

            if (blank($this->clientId) || blank($this->clientSecret)) {
                Log::error('PayPal: Credenciales incompletas para obtener access token', $this->logContext());

                return null;
            }

            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                Log::info('PayPal: Access token obtenido correctamente', $this->logContext([
                    'http_status' => $response->status(),
                ]));

                return $response->json()['access_token'];
            }

            Log::error('PayPal: Error obteniendo access token', $this->logContext([
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]));

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción obteniendo access token', $this->logContext([
                'message' => $e->getMessage(),
            ]));
            return null;
        }
    }

    /**
     * Crear orden de pago
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo' => 'required|email',
        ]);

        Log::info('PayPal: Iniciando creación de orden', $this->logContext([
            'amount' => number_format($validated['amount'], 2, '.', ''),
            'payer_email' => $this->maskedEmail($validated['correo']),
            'return_url' => config('app.frontend_url') . '/como-apoyar/paypal?success=true',
            'cancel_url' => config('app.frontend_url') . '/como-apoyar/paypal?canceled=true',
        ]));

        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('PayPal: Creación de orden detenida por falta de access token', $this->logContext([
                'amount' => number_format($validated['amount'], 2, '.', ''),
                'payer_email' => $this->maskedEmail($validated['correo']),
            ]));

            return response()->json([
                'error' => 'No se pudo autenticar con PayPal',
            ], 500);
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => config('services.paypal.currency', 'MXN'),
                                'value' => number_format($validated['amount'], 2, '.', ''),
                            ],
                            'description' => 'Donación a Escalada Libre Costa Rica',
                        ],
                    ],
                    'payer' => [
                        'name' => [
                            'given_name' => $validated['nombre'],
                            'surname' => $validated['apellido'],
                        ],
                        'email_address' => $validated['correo'],
                    ],
                    'application_context' => [
                        'brand_name' => 'Escalada Libre',
                        'landing_page' => 'BILLING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => config('app.frontend_url') . '/como-apoyar/paypal?success=true',
                        'cancel_url' => config('app.frontend_url') . '/como-apoyar/paypal?canceled=true',
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('PayPal: Orden creada correctamente', $this->logContext([
                    'http_status' => $response->status(),
                    'order_id' => $data['id'] ?? null,
                    'paypal_status' => $data['status'] ?? null,
                    'amount' => number_format($validated['amount'], 2, '.', ''),
                    'payer_email' => $this->maskedEmail($validated['correo']),
                ]));

                return response()->json($data);
            }

            Log::error('PayPal: Error creando orden', $this->logContext([
                'http_status' => $response->status(),
                'body' => $response->body(),
                'amount' => number_format($validated['amount'], 2, '.', ''),
                'payer_email' => $this->maskedEmail($validated['correo']),
            ]));

            return response()->json([
                'error' => 'No se pudo crear la orden de PayPal',
                'details' => $response->json(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción creando orden', $this->logContext([
                'message' => $e->getMessage(),
                'amount' => number_format($validated['amount'], 2, '.', ''),
                'payer_email' => $this->maskedEmail($validated['correo']),
            ]));

            return response()->json([
                'error' => 'Error procesando la solicitud',
            ], 500);
        }
    }

    /**
     * Capturar pago aprobado
     */
    public function captureOrder(Request $request, string $orderId)
    {
        $formData = $request->only(['nombre', 'apellido', 'correo', 'cantidad']);

        Log::info('PayPal: Iniciando captura de orden', $this->logContext([
            'order_id' => $orderId,
            'amount' => $formData['cantidad'] ?? null,
            'payer_email' => $this->maskedEmail($formData['correo'] ?? null),
        ]));

        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('PayPal: Captura detenida por falta de access token', $this->logContext([
                'order_id' => $orderId,
                'amount' => $formData['cantidad'] ?? null,
                'payer_email' => $this->maskedEmail($formData['correo'] ?? null),
            ]));

            return response()->json([
                'error' => 'No se pudo autenticar con PayPal',
            ], 500);
        }

        try {
            $response = Http::withToken($token)
                ->withBody('{}', 'application/json')
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $data = $response->json();

                // Guardar donación en la base de datos
                try {
                    $capture = $data['purchase_units'][0]['payments']['captures'][0] ?? null;
                    $payerEmail    = $formData['correo'] ?? $data['payer']['email_address'] ?? '';
                    $payerName     = trim(($formData['nombre'] ?? $data['payer']['name']['given_name'] ?? '') . ' ' . ($formData['apellido'] ?? $data['payer']['name']['surname'] ?? ''));
                    $donatedAmount = isset($formData['cantidad']) ? (float) $formData['cantidad'] : ($capture['amount']['value'] ?? 0);
                    $currency      = $capture['amount']['currency_code'] ?? config('services.paypal.currency', 'MXN');

                    Donation::create([
                        'paypal_order_id' => $orderId,
                        'payer_name'      => $formData['nombre'] ?? $data['payer']['name']['given_name'] ?? '',
                        'payer_last_name' => $formData['apellido'] ?? $data['payer']['name']['surname'] ?? '',
                        'payer_email'     => $payerEmail,
                        'amount'          => $donatedAmount,
                        'currency'        => $currency,
                        'status'          => $data['status'] ?? 'COMPLETED',
                        'captured_at'     => now(),
                    ]);

                    Log::info('PayPal: Donación guardada en BD', $this->logContext([
                        'order_id' => $orderId,
                        'amount' => $donatedAmount,
                        'currency' => $currency,
                        'paypal_status' => $data['status'] ?? 'COMPLETED',
                        'payer_email' => $this->maskedEmail($payerEmail),
                    ]));

                    // Enviar email de confirmación al donador vía Brevo API
                    if ($payerEmail) {
                        try {
                            $htmlContent = view('emails.donation-confirmation', [
                                'donorName' => $payerName,
                                'amount'    => number_format($donatedAmount, 2, '.', ''),
                                'currency'  => $currency,
                            ])->render();

                            $brevoResponse = Http::withHeaders([
                                'api-key'      => env('BREVO_KEY'),
                                'Content-Type' => 'application/json',
                                'Accept'       => 'application/json',
                            ])->post('https://api.brevo.com/v3/smtp/email', [
                                'sender'      => [
                                    'name'  => config('app.name', 'Escalada Libre'),
                                    'email' => 'noreply@escaladalibre.org',
                                ],
                                'to'          => [['email' => $payerEmail]],
                                'subject'     => '¡Tu donación fue exitosa! — Escalada Libre A.C.',
                                'htmlContent' => $htmlContent,
                            ]);

                            if (!$brevoResponse->successful()) {
                                Log::error('PayPal: Error enviando email de confirmación vía Brevo', [
                                    'status' => $brevoResponse->status(),
                                    'body'   => $brevoResponse->body(),
                                ]);
                            } else {
                                Log::info('PayPal: Email de confirmación enviado', [
                                    'to'        => $this->maskedEmail($payerEmail),
                                    'messageId' => $brevoResponse->json('messageId'),
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('PayPal: Error enviando email de confirmación', ['message' => $e->getMessage()]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('PayPal: Error guardando donación en BD', $this->logContext([
                        'order_id' => $orderId,
                        'message' => $e->getMessage(),
                    ]));
                }

                Log::info('PayPal: Pago capturado exitosamente', $this->logContext([
                    'order_id'    => $orderId,
                    'http_status' => $response->status(),
                    'paypal_status' => $data['status'] ?? null,
                    'payer_email' => $this->maskedEmail($data['payer']['email_address'] ?? null),
                ]));

                return response()->json($data);
            }

            Log::error('PayPal: Error capturando orden', $this->logContext([
                'order_id' => $orderId,
                'http_status' => $response->status(),
                'body' => $response->body(),
                'amount' => $formData['cantidad'] ?? null,
                'payer_email' => $this->maskedEmail($formData['correo'] ?? null),
            ]));

            return response()->json([
                'error' => 'No se pudo capturar el pago',
                'details' => $response->json(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción capturando orden', $this->logContext([
                'order_id' => $orderId,
                'message' => $e->getMessage(),
                'amount' => $formData['cantidad'] ?? null,
                'payer_email' => $this->maskedEmail($formData['correo'] ?? null),
            ]));

            return response()->json([
                'error' => 'Error procesando la captura del pago',
            ], 500);
        }
    }

    /**
     * Contar total de donaciones completadas
     */
    public function donationsCount()
    {
        $count = Donation::count();
        return response()->json(['count' => $count]);
    }

    /**
     * Obtener detalles de una orden
     */
    public function getOrder(string $orderId)
    {
        Log::info('PayPal: Iniciando consulta de orden', $this->logContext([
            'order_id' => $orderId,
        ]));

        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('PayPal: Consulta detenida por falta de access token', $this->logContext([
                'order_id' => $orderId,
            ]));

            return response()->json([
                'error' => 'No se pudo autenticar con PayPal',
            ], 500);
        }

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                $data = $response->json();

                Log::info('PayPal: Orden consultada correctamente', $this->logContext([
                    'order_id' => $orderId,
                    'http_status' => $response->status(),
                    'paypal_status' => $data['status'] ?? null,
                ]));

                return response()->json($data);
            }

            Log::error('PayPal: Error consultando orden', $this->logContext([
                'order_id' => $orderId,
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]));

            return response()->json([
                'error' => 'No se pudo obtener la orden',
            ], 404);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción obteniendo orden', $this->logContext([
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]));

            return response()->json([
                'error' => 'Error obteniendo la orden',
            ], 500);
        }
    }
}
