<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($validated['email']));

        // Registrar o actualizar suscriptor
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $email],
            ['status' => 'active', 'subscribed_at' => now()]
        );

        if (!$subscriber->wasRecentlyCreated && $subscriber->status === 'active') {
            return response()->json(['message' => 'Ya estás suscrito.'], 200);
        }

        if (!$subscriber->wasRecentlyCreated) {
            $subscriber->update(['status' => 'active']);
        }

        // Enviar email de bienvenida vía Brevo API
        $this->sendWelcomeEmail($email);

        return response()->json(['message' => '¡Suscripción exitosa!'], 201);
    }

    private function sendWelcomeEmail(string $email): void
    {
        $appName = config('app.name', 'Escalada Libre');

        Http::withHeaders([
            'api-key' => env('BREVO_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => $appName,
                'email' => 'noreply@escaladalibre.org',
            ],
            'to' => [['email' => $email]],
            'subject' => '¡Gracias por suscribirte a ' . $appName . '!',
            'htmlContent' => $this->buildEmailHtml($appName),
        ]);
    }

    private function buildEmailHtml(string $appName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">
          <tr>
            <td style="background:#F9D363;padding:40px 48px 32px;">
              <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase;color:#000;">NEWSLETTER</p>
              <h1 style="margin:0;font-size:28px;font-weight:700;color:#0D0D0D;line-height:1.3;">¡Gracias por suscribirte!</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:40px 48px;">
              <p style="margin:0 0 16px;font-size:16px;color:#444;line-height:1.6;">
                Te has suscrito exitosamente al newsletter de <strong>{$appName}</strong>.
              </p>
              <p style="margin:0 0 16px;font-size:16px;color:#444;line-height:1.6;">
                A partir de ahora recibirás noticias, actividades y novedades de nuestra asociación directamente en tu correo.
              </p>
              <p style="margin:32px 0 0;font-size:14px;color:#888;line-height:1.5;">
                Si no solicitaste esta suscripción, puedes ignorar este mensaje.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background:#f5f5f5;padding:24px 48px;text-align:center;">
              <p style="margin:0;font-size:12px;color:#aaa;">&copy; {$appName} — Todos los derechos reservados.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
