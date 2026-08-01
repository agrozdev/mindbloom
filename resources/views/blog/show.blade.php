@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $post->title }}</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('blog.index') }}" class="mad-link">Новини</a></span> /
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
          <div class="mad-entity-tags content-element-3">
            @if ($post->category)
              <span>{{ $post->category }}</span>
            @endif
            @if ($post->published_at)
              <span>{{ $post->published_at->translatedFormat('j F Y') }}</span>
            @endif
          </div>
          <div class="mad-text-medium">
            {!! $post->body !!}
          </div>
        </div>
        <div class="col-lg-4">
          <h6 class="mad-widget-title">Още новини</h6>
          <ul class="mad-vr-list">
            @foreach (\App\Models\Post::published()->where('id', '!=', $post->id)->limit(6)->get() as $other)
              <li><a href="{{ route('blog.show', $other) }}">{{ $other->title }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
