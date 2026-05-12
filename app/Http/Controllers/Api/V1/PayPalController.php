<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Obtener token de acceso de PayPal
     */
    private function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('PayPal: Error obteniendo access token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción obteniendo access token', [
                'message' => $e->getMessage(),
            ]);
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

        $token = $this->getAccessToken();

        if (!$token) {
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
                                'currency_code' => 'USD',
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
                return response()->json($response->json());
            }

            Log::error('PayPal: Error creando orden', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'No se pudo crear la orden de PayPal',
                'details' => $response->json(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción creando orden', [
                'message' => $e->getMessage(),
            ]);

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
        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json([
                'error' => 'No se pudo autenticar con PayPal',
            ], 500);
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $data = $response->json();

                // Aquí podrías guardar la transacción en la base de datos
                Log::info('PayPal: Pago capturado exitosamente', [
                    'order_id' => $orderId,
                    'status' => $data['status'] ?? null,
                    'payer_email' => $data['payer']['email_address'] ?? null,
                ]);

                return response()->json($data);
            }

            Log::error('PayPal: Error capturando orden', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'No se pudo capturar el pago',
                'details' => $response->json(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción capturando orden', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error procesando la captura del pago',
            ], 500);
        }
    }

    /**
     * Obtener detalles de una orden
     */
    public function getOrder(string $orderId)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json([
                'error' => 'No se pudo autenticar con PayPal',
            ], 500);
        }

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'No se pudo obtener la orden',
            ], 404);
        } catch (\Exception $e) {
            Log::error('PayPal: Excepción obteniendo orden', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error obteniendo la orden',
            ], 500);
        }
    }
}
