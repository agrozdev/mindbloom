<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $emailSubject }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Marck+Script&display=swap" rel="stylesheet" />
  <style>
    .mb-body a { color: #A31C1C; }
    .mb-body p { margin: 0 0 1em; }
    .mb-body ul, .mb-body ol { margin: 0 0 1em; padding-left: 1.25em; }
    @media (max-width: 600px) { .mb-pad { padding: 1.25rem !important; } .mb-banner-title { font-size: 1.5rem !important; } }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#F4F4F5; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F4F5; padding:2rem 0;">
    <tr>
      <td align="center" style="padding:0 0.5rem;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;">
          <tr>
            <td align="center" style="padding:1.25rem 2rem; background-color:#ffffff; border-bottom:3px solid #A31C1C;" class="mb-pad">
              <a href="{{ url('/') }}" style="text-decoration:none;">
                <img src="{{ asset('images/logo-mindbloom.png') }}" width="160" height="55" alt="MindBloom" style="display:block; border:0; width:160px; height:auto; color:#A31C1C; font-size:1.25rem; font-weight:bold;" />
              </a>
            </td>
          </tr>
          @if ($bannerUrl)
            <tr>
              {{-- Background image with a solid fallback colour for clients that drop it (Outlook desktop). --}}
              <td align="center" valign="middle" background="{{ $bannerUrl }}" bgcolor="#4A2A5E" height="160" style="height:160px; padding:1.5rem 2rem; background-color:#4A2A5E; background-image:url('{{ $bannerUrl }}'); background-size:cover; background-position:center;" class="mb-pad">
                <div style="font-family:'Marck Script', Georgia, 'Times New Roman', serif; font-size:1.875rem; line-height:1.25; color:#ffffff; text-shadow:0 1px 6px rgba(0,0,0,.55);" class="mb-banner-title">
                  {{ $storyTitle ?: 'Вдъхновяващи истории' }}
                </div>
              </td>
            </tr>
          @endif
          <tr>
            <td style="padding:2rem;" class="mb-pad">
              <div class="mb-body" style="font-size:1rem; line-height:1.6; color:#404957;">
                {!! $htmlBody !!}
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:1.25rem 2rem; background-color:#F6F6F6; font-size:0.75rem; line-height:1.5; color:#707781;" class="mb-pad">
              Получавате този имейл, защото сте се регистрирали на mindbloombg.com.<br>
              <a href="{{ $unsubscribeUrl }}" style="color:#707781;">Отписване</a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
