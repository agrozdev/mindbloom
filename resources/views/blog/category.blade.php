@extends('layouts.app')

@section('title', $activeCategory->name)
@section('meta_description', 'Вдъхновяващи истории и статии от практиката на MindBloom в категория ' . $activeCategory->name . '.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}'); background-position:15% center;">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $activeCategory->name }}</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('blog.index') }}" class="mad-link">Вдъхновяващи истории</a></span> /
        <span>{{ $activeCategory->name }}</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="mad-entities mad-text-medium with-date type-2">
            @foreach ($posts as $post)
              <div class="mad-col">
                <article class="mad-entity">
                  @if ($post->featured_image)
                    <div class="mad-entity-media">
                      <a href="{{ route('blog.show', [$post->category, $post]) }}"><img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }} — приказка с поука" /></a>
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
                          <span>{{ $post->category->name }}</span>
                        @endif
                      </div>
                      <h4 class="mad-entity-title">
                        <a href="{{ route('blog.show', [$post->category, $post]) }}">{{ $post->title }}</a>
                      </h4>
                      <p>{{ $post->excerpt }}</p>
                      <div class="mad-entity-footer">
                        <a href="{{ route('blog.show', [$post->category, $post]) }}" class="mad-text-link">Прочети още</a>
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            @endforeach
          </div>

          @if ($posts->isEmpty())
            <p class="text-center">Все още няма публикувани истории в тази категория.</p>
          @endif

          {{ $posts->links() }}
        </div>
        <div class="col-lg-4">
          <h6 class="mad-widget-title">Категории</h6>
          <ul class="mad-vr-list mad-categories-list">
            <li>
              <a href="{{ route('blog.index') }}">&larr; Всички категории</a>
            </li>
            @foreach ($categories as $category)
              <li class="{{ $activeCategory->is($category) ? 'is-active' : '' }}">
                <a href="{{ route('blog.category', $category) }}">{{ $category->name }}</a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
