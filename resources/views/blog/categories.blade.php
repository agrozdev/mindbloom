@extends('layouts.app')

@section('title', 'Вдъхновяващи истории')
@section('meta_description', 'Вдъхновяващи истории и статии от практиката на MindBloom, подредени по категории.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Вдъхновяващи истории</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Вдъхновяващи истории</span>
      </nav>
    </div>
  </div>

  <div class="mad-content no-pd">
    <div class="container-fluid">
      <div class="mad-section">
        <div class="mad-icon-boxes grid-type item-col-4">
          @foreach ($categories as $category)
            <div class="mad-col">
              <a href="{{ route('blog.category', $category) }}" class="mad-icon-box">
                <div class="mad-icon-box-content">
                  <h6 class="mad-icon-box-title">{{ $category->name }}</h6>
                  <p>{{ $category->posts_count }} {{ $category->posts_count == 1 ? 'история' : 'истории' }}</p>
                  <div class="mad-text-link">Разгледай</div>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        @if ($categories->isEmpty())
          <p class="text-center">Все още няма създадени категории.</p>
        @endif
      </div>
    </div>
  </div>
@endsection
