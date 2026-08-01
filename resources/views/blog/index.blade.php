@extends('layouts.app')

@section('title', 'Вдъхновяващи истории')
@section('meta_description', 'Вдъхновяващи истории и статии от практиката на MindBloom.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Вдъхновяващи истории</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Вдъхновяващи истории</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="mad-entities mad-text-medium with-date type-2">
        @foreach ($posts as $post)
          <div class="mad-col">
            <article class="mad-entity">
              @if ($post->featured_image)
                <div class="mad-entity-media">
                  <a href="{{ route('blog.show', $post) }}"><img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" /></a>
                </div>
              @endif
              <div class="mad-entity-content">
                @if ($post->published_at)
                  <div class="mad-entity-date">
                    <span>{{ $post->published_at->format('d') }}</span>
                    <span>{{ $post->published_at->translatedFormat('M') }}</span>
                  </div>
                @endif
                <div class="mad-entity-header">
                  <div class="mad-entity-tags">
                    @if ($post->category)
                      <span>{{ $post->category }}</span>
                    @endif
                  </div>
                  <h4 class="mad-entity-title">
                    <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                  </h4>
                  <p>{{ $post->excerpt }}</p>
                  <div class="mad-entity-footer">
                    <a href="{{ route('blog.show', $post) }}" class="mad-text-link">Прочети още</a>
                  </div>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if ($posts->isEmpty())
        <p class="text-center">Все още няма публикувани новини.</p>
      @endif

      {{ $posts->links() }}
    </div>
  </div>
@endsection
