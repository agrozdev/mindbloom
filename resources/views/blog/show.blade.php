@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $post->title }}</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('blog.index') }}" class="mad-link">Вдъхновяващи истории</a></span> /
        <span>{{ $post->title }}</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          @if ($post->featured_image)
            <div class="mad-entity-media content-element-4">
              <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" />
            </div>
          @endif
          <div class="content-element-3" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; column-gap:1.5rem; row-gap:1rem;">
            <div class="mad-entity-tags" style="margin:0;">
              @if ($post->category)
                <a href="{{ route('blog.category', $post->category) }}">{{ $post->category->name }}</a>
              @endif
              @if ($post->published_at)
                <span>{{ $post->published_at->translatedFormat('j F Y') }}</span>
              @endif
            </div>
            <a href="#" data-arctic-modal="#post-question-modal" class="btn">Помисли и сподели</a>
          </div>

          @if (session('question_status'))
            <div class="mad-alert-box mad-alert-box--success content-element-3">
              <div class="mad-alert-box-inner">{{ session('question_status') }}</div>
            </div>
          @endif

          <div class="mad-text-medium">
            {!! $post->body !!}
          </div>
        </div>
        <div class="col-lg-4">
          <h6 class="mad-widget-title">Още истории</h6>
          <ul class="mad-vr-list">
            @foreach (\App\Models\Post::published()->where('id', '!=', $post->id)->limit(6)->get() as $other)
              <li><a href="{{ route('blog.show', [$other->category, $other]) }}">{{ $other->title }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="mad-d-none">
    <div id="post-question-modal" class="mad-modal mad-modal--form">
      <h6 class="mad-title">Въпрос към историята</h6>
      <form method="POST" action="{{ route('blog.question', [$post->category, $post]) }}" class="mad-form" novalidate data-validate>
        @csrf
        <div class="mad-form-honeypot" aria-hidden="true">
          <label for="question_website">Website</label>
          <input type="text" id="question_website" name="website" tabindex="-1" autocomplete="off" />
        </div>
        <input type="hidden" name="form_rendered_at" value="{{ now()->timestamp }}" />
        <div class="mad-form-item">
          <label>Вашето име *</label>
          <input type="text" name="name" required data-error="Моля, въведете вашето име." placeholder="Вашето име" />
        </div>
        <div class="mad-form-item">
          <label>Имейл адрес *</label>
          <input type="email" name="email" required data-error="Моля, въведете валиден имейл адрес." placeholder="Имейл адрес" />
        </div>
        <div class="mad-form-item">
          <label>Телефон (незадължително)</label>
          <input type="tel" name="phone" placeholder="Телефонен номер" />
        </div>
        <div class="mad-form-item">
          <label>Въпрос *</label>
          <textarea rows="4" name="question" required data-error="Моля, въведете въпроса си." placeholder="Твоят въпрос към тази история"></textarea>
        </div>
        <div class="mad-form-item">
          <button type="submit" class="btn btn-big">Изпрати</button>
        </div>
      </form>
      <button type="button" class="arcticmodal-close">
        <i class="material-icons">close</i>
      </button>
    </div>
  </div>
@endsection
