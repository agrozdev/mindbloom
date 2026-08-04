<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('subject', 'MindBloom')</title>
</head>
<body style="margin:0; padding:0; background-color:#F4F4F5; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F4F5; padding:2rem 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;">
          <tr>
            <td style="background-color:#A31C1C; padding:1.5rem 2rem;">
              <span style="font-size:1.25rem; font-weight:bold; color:#ffffff; letter-spacing:0.5px;">MindBloom</span>
            </td>
          </tr>
          <tr>
            <td style="padding:2rem;">
              <h1 style="margin:0 0 1.5rem; font-size:1.125rem; color:#1A1A1A;">@yield('heading')</h1>
              <div style="font-size:0.9375rem; line-height:1.6; color:#404957;">
                @yield('content')
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:1.25rem 2rem; background-color:#F6F6F6; font-size:0.75rem; color:#707781;">
              Това съобщение е изпратено автоматично от формата на уебсайта mindbloombg.com.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
