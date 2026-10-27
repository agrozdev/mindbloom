@extends('readers.layout')

@section('title', 'Вашето мнение')
@section('page_heading', 'Вашето мнение')

@section('page_body')
  @if (session('feedback_status') || $alreadySent)
    <div class="mad-alert-box mad-alert-box--success content-element-3" role="status">
      <div class="mad-alert-box-inner">Благодарим ви! Вашето мнение е много ценно за нас.</div>
    </div>
    <p><a href="{{ route('blog.show', [$post->category, $post]) }}" class="btn">Обратно към приказката</a></p>
  @else
    <p class="content-element-3">
      Здравейте, {{ $reader->firstName() }}! Как ви се стори приказката
      <a href="{{ route('blog.show', [$post->category, $post]) }}">„{{ $post->title }}“</a>?
    </p>

    <form method="POST" action="{{ route('feedback.store', $token) }}" class="mad-form" novalidate>
      @csrf
      <div class="mad-form-honeypot" aria-hidden="true">
        <label for="feedback_website">Website</label>
        <input type="text" id="feedback_website" name="website" tabindex="-1" autocomplete="off" />
      </div>

      <fieldset class="rating-input @error('rating') has-error @enderror" aria-describedby="rating_error">
        <legend>Оценка</legend>
        <div class="rating-input-stars">
          @for ($i = 5; $i >= 1; $i--)
            <input type="radio" id="rating_{{ $i }}" name="rating" value="{{ $i }}" @checked((int) $rating === $i) required />
            <label for="rating_{{ $i }}" title="{{ $i }} от 5"><span aria-hidden="true">★</span><span class="reader-sr-only">{{ $i }} от 5</span></label>
          @endfor
        </div>
        @error('rating')<span class="mad-error-message" id="rating_error" role="alert">{{ $message }}</span>@enderror
      </fieldset>

      <div class="mad-form-item @error('answer_feeling') has-error @enderror">
        <label for="answer_feeling">Какво почувствахте, докато четяхте?</label>
        <textarea id="answer_feeling" name="answer_feeling" rows="4" maxlength="3000">{{ old('answer_feeling') }}</textarea>
        @error('answer_feeling')<span class="mad-error-message" role="alert">{{ $message }}</span>@enderror
      </div>

      <div class="mad-form-item @error('answer_lesson') has-error @enderror">
        <label for="answer_lesson">Какво послание или урок открихте в приказката?</label>
        <textarea id="answer_lesson" name="answer_lesson" rows="4" maxlength="3000">{{ old('answer_lesson') }}</textarea>
        @error('answer_lesson')<span class="mad-error-message" role="alert">{{ $message }}</span>@enderror
      </div>

      <div class="mad-form-item reader-page-checkbox">
        <input type="checkbox" id="can_publish" name="can_publish" value="1" @checked(old('can_publish')) />
        <label for="can_publish">
          <span>Може да публикувате отзива ми с първото ми име.</span>
        </label>
      </div>

      <button type="submit" class="btn btn-big">Изпрати мнението си</button>
    </form>
  @endif
@endsection
