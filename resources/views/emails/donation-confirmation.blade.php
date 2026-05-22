<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tu donación fue exitosa — Escalada Libre A.C.</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #ffffff;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #333333;
    }
    table { border-collapse: collapse; }
    a { color: #333333; }
  </style>
</head>
<body>
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
    <tr>
      <td align="center">

        <!-- Contenedor principal -->
        <table width="520" cellpadding="0" cellspacing="0"
               style="max-width:520px; width:100%; background:#ffffff;">

          <!-- Logo -->
          <tr>
            <td align="center" style="padding: 48px 32px 24px;">
              <img
                src="{{ config('app.frontend_url') }}/images/logoescalada.png"
                alt="Escalada Libre A.C."
                width="120"
                style="display:block; width:120px; height:auto;"
              />
              <p style="margin: 14px 0 0; font-size: 13px; font-weight: 700;
                         letter-spacing: 0.25em; text-transform: uppercase;
                         color: #111111; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                ESCALADA<br>LIBRE A.C.
              </p>
            </td>
          </tr>

          <!-- Divisor -->
          <tr>
            <td style="padding: 0 32px;">
              <hr style="border:none; border-top: 1px solid #cccccc; margin:0;" />
            </td>
          </tr>

          <!-- Banner verde -->
          <tr>
            <td align="center" style="padding: 40px 32px 0;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center"
                      style="background-color: #00e676; padding: 16px 32px;
                             border-radius: 4px;">
                    <span style="font-size: 18px; font-weight: 700;
                                 color: #000000; letter-spacing: 0.02em;">
                      Tu donación fue exitosa
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Mensaje de agradecimiento -->
          <tr>
            <td align="center" style="padding: 40px 32px 0;">
              <p style="margin:0; font-size: 15px; color: #555555; line-height: 1.6; text-align:center;">
                Te agradecemos tu<br>invaluable donación
              </p>
            </td>
          </tr>

          @if($donorName)
          <tr>
            <td align="center" style="padding: 8px 32px 0;">
              <p style="margin:0; font-size: 15px; color: #555555; text-align:center;">
                {{ $donorName }},
              </p>
            </td>
          </tr>
          @endif

          <!-- ¡Gracias! -->
          <tr>
            <td align="center" style="padding: 24px 32px 0;">
              <p style="margin:0; font-size: 36px; font-weight: 700;
                         color: #111111; text-align:center;">
                ¡Gracias!
              </p>
            </td>
          </tr>

          @if($amount)
          <tr>
            <td align="center" style="padding: 16px 32px 0;">
              <p style="margin:0; font-size: 14px; color: #777777; text-align:center;">
                Monto donado: <strong>{{ $currency }} ${{ $amount }}</strong>
              </p>
            </td>
          </tr>
          @endif

          <!-- Contáctanos -->
          <tr>
            <td align="center" style="padding: 40px 32px 48px;">
              <a href="{{ config('app.frontend_url') }}/contacto"
                 style="font-size: 14px; color: #333333; text-decoration: underline;">
                Contáctanos
              </a>
            </td>
          </tr>

          <!-- Divisor footer -->
          <tr>
            <td style="padding: 0 32px;">
              <hr style="border:none; border-top: 1px solid #cccccc; margin:0;" />
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding: 20px 32px 32px;">
              <p style="margin:0; font-size: 11px; color: #999999; text-align:center; line-height:1.5;">
                Copyright &copy; {{ date('Y') }}, Todos los derechos reservados Escalada Libre A.C.<br>
                Este sitio fue donado por el despacho COBO's
              </p>
            </td>
          </tr>

        </table>
        <!-- /Contenedor principal -->

      </td>
    </tr>
  </table>
</body>
</html>
