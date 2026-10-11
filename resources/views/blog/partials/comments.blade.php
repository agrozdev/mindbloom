@php
  $commentErrors = $errors->getBag('comment');
  $commentFormOpen = $commentErrors->any();
  $ordinals = [3 => 'третата', 4 => 'четвъртата', 5 => 'петата', 6 => 'шестата', 7 => 'седмата', 8 => 'осмата', 9 => 'деветата', 10 => 'десетата', 11 => 'единадесетата', 12 => 'дванадесетата'];
@endphp

<section id="comments" class="mad-story-comments">
  @if ($comments->isNotEmpty())
    <h2 class="mad-story-comments-title">Какво споделиха читателите</h2>
    <ul class="mad-story-comments-list">
      @foreach ($comments as $comment)
        <li class="mad-story-comment">
          <div class="mad-story-comment-meta">
            <strong>{{ $comment->name }}</strong>
            <span>{{ $comment->approved_at->translatedFormat('j F Y') }}</span>
          </div>
          <p>{{ $comment->body }}</p>
        </li>
      @endforeach
    </ul>
  @endif

  @if (session('comment_status'))
    <div class="mad-alert-box mad-alert-box--success">
      <div class="mad-alert-box-inner">{{ session('comment_status') }}</div>
    </div>
  @endif

  <div class="mad-story-invite">
    <p>
      Историята свърши… но не съвсем. Всяка приказка продължава да живее, докато някой мисли за нея.
    </p>
    <p>
      Ако тази остави в теб нещо мъничко — усмивка, въпрос или спомен — напиши ни. Твоите думи са като
      вълшебно фенерче: показват ни, че не пишем в тъмното, и ни дават смелост да разкажем следващата история.
    </p>
    <button type="button" class="btn" data-comment-toggle @if ($commentFormOpen) hidden @endif>Остави своя коментар</button>
  </div>

  <div id="comment-form-wrap" @unless ($commentFormOpen) hidden @endunless>
    <form method="POST" action="{{ route('blog.comment', [$post->category, $post]) }}" class="mad-form" novalidate data-validate>
      @csrf
      <div class="mad-form-honeypot" aria-hidden="true">
        <label for="comment_website">Website</label>
        <input type="text" id="comment_website" name="website" tabindex="-1" autocomplete="off" />
      </div>
      {{-- Keep the original render time after a failed answer, so a reader who fixes
           the word quickly isn't mistaken for a bot by the minimum fill-time check. --}}
      <input type="hidden" name="form_rendered_at" value="{{ old('form_rendered_at', now()->timestamp) }}" />
      <div class="mad-form-item @if ($commentErrors->has('name')) has-error @endif">
        <label for="comment_name">Твоето име *</label>
        <input type="text" id="comment_name" name="name" value="{{ old('name') }}" required data-error="Моля, въведете вашето име." placeholder="Твоето име" />
        @if ($commentErrors->has('name'))<span class="mad-error-message">{{ $commentErrors->first('name') }}</span>@endif
      </div>
      <div class="mad-form-item @if ($commentErrors->has('email')) has-error @endif">
        <label for="comment_email">Имейл адрес * <small>(няма да бъде публикуван)</small></label>
        <input type="email" id="comment_email" name="email" value="{{ old('email') }}" required data-error="Моля, въведете валиден имейл адрес." placeholder="Имейл адрес" />
        @if ($commentErrors->has('email'))<span class="mad-error-message">{{ $commentErrors->first('email') }}</span>@endif
      </div>
      <div class="mad-form-item @if ($commentErrors->has('body')) has-error @endif">
        <label for="comment_body">Коментар *</label>
        <textarea rows="5" id="comment_body" name="body" required data-error="Моля, напишете коментара си." placeholder="Какво ти остави тази история?">{{ old('body') }}</textarea>
        @if ($commentErrors->has('body'))<span class="mad-error-message">{{ $commentErrors->first('body') }}</span>@endif
      </div>
      @if ($commentChallenge)
        @php
          $hintLetters = session('comment_hint_letters', 1);
          $hint = mb_strtolower(mb_substr($commentChallenge['word'], 0, $hintLetters));
          $ordinal = $ordinals[$commentChallenge['position']] ?? $commentChallenge['position'] . '-ата';
        @endphp
        <div class="mad-form-item @if ($commentErrors->has('challenge')) has-error @endif">
          <label for="comment_challenge">Малка загадка за внимателния читател *</label>
          <p class="mad-story-challenge">
            Коя е {{ $ordinal }} дума от историята? Подсказка: започва с „{{ $hint }}…“
          </p>
          <input type="text" id="comment_challenge" name="challenge" required autocomplete="off" data-error="Моля, отговорете на въпроса." placeholder="Напиши думата" />
          @if ($commentErrors->has('challenge'))<span class="mad-error-message">{{ $commentErrors->first('challenge') }}</span>@endif
        </div>
      @endif
      <div class="mad-form-item">
        <p class="mad-story-comments-note">Коментарът ще се появи на страницата, след като го прочетем и одобрим.</p>
        <button type="submit" class="btn btn-big">Изпрати коментара</button>
      </div>
    </form>
  </div>
</section>

@push('styles')
  <style>
    .mad-story-comments { margin-top: 3rem; padding-top: 2.5rem; border-top: 1px solid var(--color-primary-25); }
    .mad-story-comments-title { font-size: 1.5rem; margin-bottom: 1.5rem; }
    .mad-story-comments-list { list-style: none; padding: 0; margin: 0 0 2.5rem; }
    .mad-story-comment { background: var(--color-primary-5); border-radius: var(--br-main); padding: 1.25rem 1.5rem; }
    .mad-story-comment + .mad-story-comment { margin-top: 1rem; }
    .mad-story-comment-meta { display: flex; flex-wrap: wrap; gap: .25rem 1rem; align-items: baseline; margin-bottom: .5rem; }
    .mad-story-comment-meta span { font-size: .875rem; opacity: .7; }
    .mad-story-comment p { margin: 0; white-space: pre-line; overflow-wrap: anywhere; }
    .mad-story-invite { background: var(--color-primary-10); color: var(--color-primary-darker); border-radius: var(--br-main); padding: 1.5rem; margin-bottom: 2rem; }
    .mad-story-invite p { font-family: 'Marck Script', cursive; font-size: 1.375rem; line-height: 2.25rem; margin: 0 0 1rem; }
    .mad-story-invite .btn { margin-top: .5rem; }
    .mad-story-challenge { font-family: 'Marck Script', cursive; font-size: 1.25rem; margin: 0 0 .75rem; color: var(--color-primary-darker); }
    .mad-story-comments [hidden] { display: none !important; }
    .mad-story-comments .mad-form-item { display: block; width: 100%; }
    .mad-story-comments .mad-form-item input, .mad-story-comments .mad-form-item textarea { width: 100%; }
    .mad-story-comments-note { font-size: .875rem; opacity: .75; margin-bottom: 1rem; }
  </style>
@endpush

@push('scripts')
  <script>
    document.querySelectorAll('[data-comment-toggle]').forEach(function (button) {
      button.addEventListener('click', function () {
        var wrap = document.getElementById('comment-form-wrap');
        wrap.hidden = false;
        button.hidden = true;
        wrap.querySelector('input:not([type=hidden]):not([tabindex="-1"])').focus();
      });
    });
  </script>
@endpush
