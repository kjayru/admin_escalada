<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestBrevoEmail extends Command
{
    protected $signature = 'mail:test-brevo
                            {email? : Dirección de destino (default: MAIL_FROM_ADDRESS)}
                            {--from= : Email remitente (default: noreply@escaladalibre.org)}
                            {--name= : Nombre remitente (default: APP_NAME)}
                            {--type=generic : Tipo de email: generic | donation}
                            {--donor= : Nombre del donador (solo con --type=donation)}
                            {--amount=100.00 : Monto de prueba (solo con --type=donation)}
                            {--currency=MXN : Moneda (solo con --type=donation)}';

    protected $description = 'Envía un email de prueba usando la API de Brevo (BREVO_KEY). Tipos: generic, donation';

    public function handle(): int
    {
        $apiKey = env('BREVO_KEY');

        if (empty($apiKey)) {
            $this->error('BREVO_KEY no está definida en el archivo .env');
            return self::FAILURE;
        }

        $toEmail   = $this->argument('email') ?? env('MAIL_FROM_ADDRESS', 'test@example.com');
        $fromEmail = $this->option('from')    ?? 'noreply@escaladalibre.org';
        $fromName  = $this->option('name')    ?? config('app.name', 'Escalada PRO');
        $type      = $this->option('type');

        $this->info("Enviando email de prueba ({$type})...");
        $this->line("  De      : {$fromName} <{$fromEmail}>");
        $this->line("  Para    : {$toEmail}");
        $this->line("  Entorno : " . app()->environment());

        [$subject, $htmlContent] = match ($type) {
            'donation' => $this->buildDonationEmail(),
            default    => $this->buildGenericEmail($fromName),
        };

        $response = Http::withHeaders([
            'api-key'      => $apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender'      => ['name' => $fromName, 'email' => $fromEmail],
            'to'          => [['email' => $toEmail]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        if ($response->successful()) {
            $messageId = $response->json('messageId') ?? 'N/A';
            $this->info("✓ Email enviado correctamente.");
            $this->line("  messageId: {$messageId}");
            return self::SUCCESS;
        }

        $this->error("✗ Error al enviar el email.");
        $this->line("  HTTP Status : " . $response->status());
        $this->line("  Respuesta   : " . $response->body());
        return self::FAILURE;
    }

    /** @return array{string, string} [subject, htmlContent] */
    private function buildDonationEmail(): array
    {
        $donorName = $this->option('donor')    ?? 'Donador de Prueba';
        $amount    = $this->option('amount')   ?? '100.00';
        $currency  = $this->option('currency') ?? 'MXN';

        $subject = '[TEST] ¡Tu donación fue exitosa! — Escalada Libre A.C.';
        $html    = view('emails.donation-confirmation', [
            'donorName' => $donorName,
            'amount'    => $amount,
            'currency'  => $currency,
        ])->render();

        return [$subject, $html];
    }

    /** @return array{string, string} [subject, htmlContent] */
    private function buildGenericEmail(string $appName): array
    {
        $env  = app()->environment();
        $date = now()->format('d/m/Y H:i:s');
        $url  = config('app.url');

        $subject = '[TEST] Email de prueba — ' . $appName . ' (' . $date . ')';
        $html    = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0"
               style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">
          <tr>
            <td style="background:#F9D363;padding:32px 48px 24px;">
              <p style="margin:0 0 6px;font-size:11px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase;color:#000;">
                TEST DE ENVÍO
              </p>
              <h1 style="margin:0;font-size:26px;font-weight:700;color:#0D0D0D;line-height:1.3;">
                Email de prueba — {$appName}
              </h1>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 48px;">
              <p style="margin:0 0 16px;font-size:15px;color:#444;line-height:1.6;">
                Este mensaje confirma que la integración con <strong>Brevo</strong> funciona correctamente.
              </p>
              <table cellpadding="0" cellspacing="0"
                     style="background:#f9f9f9;border-radius:6px;padding:16px 20px;width:100%;border:1px solid #e8e8e8;">
                <tr>
                  <td style="font-size:13px;color:#666;padding:4px 0;">
                    <strong>Entorno:</strong> {$env}
                  </td>
                </tr>
                <tr>
                  <td style="font-size:13px;color:#666;padding:4px 0;">
                    <strong>Fecha:</strong> {$date}
                  </td>
                </tr>
                <tr>
                  <td style="font-size:13px;color:#666;padding:4px 0;">
                    <strong>APP_URL:</strong> {$url}
                  </td>
                </tr>
              </table>
              <p style="margin:24px 0 0;font-size:13px;color:#999;line-height:1.5;">
                Si no esperabas este correo, puedes ignorarlo.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return [$subject, $html];
    }
}
