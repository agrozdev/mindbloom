@php
  $privacyUrl = \App\Models\Setting::get('privacy_policy_url') ?: route('legal.privacy');
  $turnstileKey = \App\Models\Setting::get('turnstile_secret_key') ? \App\Models\Setting::get('turnstile_site_key') : null;
@endphp

<section class="story-gate" id="story-gate" aria-labelledby="story-gate-title">
  <h2 class="story-gate-title" id="story-gate-title">Искате ли да прочетете приказката докрай?</h2>
  <p class="story-gate-text">
    Оставете името и имейла си и приказката ще се отвори веднага. След няколко дни може да ви пишем,
    за да ни разкажете как ви е докоснала – вашето мнение ни помага да създаваме нови приказки.
    Без спам. Можете да се отпишете по всяко време.
  </p>

  <form class="mad-form story-gate-form" method="POST" action="{{ route('readers.signup') }}" novalidate data-story-gate data-track-url="{{ route('readers.track') }}">
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}" />
    <div class="mad-form-honeypot" aria-hidden="true">
      <label for="gate_website">Website</label>
      <input type="text" id="gate_website" name="website" tabindex="-1" autocomplete="off" />
    </div>

    <div class="story-gate-fields">
      <div class="mad-form-item" data-field="name">
        <label for="gate_name">Име</label>
        <input type="text" id="gate_name" name="name" autocomplete="given-name" required maxlength="100" aria-describedby="gate_name_error" />
        <span class="mad-error-message" id="gate_name_error" role="alert" hidden></span>
      </div>
      <div class="mad-form-item" data-field="email">
        <label for="gate_email">Имейл</label>
        <input type="email" id="gate_email" name="email" autocomplete="email" required maxlength="255" aria-describedby="gate_email_error" />
        <span class="mad-error-message" id="gate_email_error" role="alert" hidden></span>
      </div>
    </div>

    <div class="mad-form-item story-gate-consent" data-field="consent">
      <input type="checkbox" id="gate_consent" name="consent" value="1" required aria-describedby="gate_consent_error" />
      <label for="gate_consent">
        <span>Съгласен/на съм MindBloom да ми изпраща имейли, свързани с приказките, и да запазва кои приказки съм прочел/а, за да получавам по-подходящо съдържание, съгласно <a href="{{ $privacyUrl }}" target="_blank" rel="noopener">Политиката за поверителност</a>.</span>
      </label>
      <span class="mad-error-message" id="gate_consent_error" role="alert" hidden></span>
    </div>

    @if ($turnstileKey)
      <div class="mad-form-item" data-field="turnstile">
        <div class="cf-turnstile" data-sitekey="{{ $turnstileKey }}" data-language="bg"></div>
        <span class="mad-error-message" role="alert" hidden></span>
      </div>
      @push('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
      @endpush
    @endif

    <p class="story-gate-general-error mad-error-message" role="alert" hidden></p>
    <button type="submit" class="btn btn-big">Прочети цялата приказка</button>
  </form>
</section>
