@if ($reviews->isNotEmpty())
  <section class="story-reviews" id="otzivi" aria-labelledby="story-reviews-title">
    <h2 class="story-reviews-title" id="story-reviews-title">Отзиви от читатели</h2>
    <ul class="story-reviews-list">
      @foreach ($reviews as $review)
        <li class="story-review">
          <div class="story-review-meta">
            <strong>{{ $review->reader->firstName() }}</strong>
            <span class="story-review-stars" role="img" aria-label="Оценка {{ $review->rating }} от 5">
              @for ($i = 1; $i <= 5; $i++)<span aria-hidden="true" class="{{ $i <= $review->rating ? 'is-on' : '' }}">★</span>@endfor
            </span>
            <span class="story-review-date">{{ $review->created_at->translatedFormat('j F Y') }}</span>
          </div>
          @if ($review->answer_feeling)
            <p>{{ $review->answer_feeling }}</p>
          @endif
          @if ($review->answer_lesson)
            <p>{{ $review->answer_lesson }}</p>
          @endif
        </li>
      @endforeach
    </ul>
  </section>
@endif
