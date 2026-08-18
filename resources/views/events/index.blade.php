@extends('layouts.app')

@section('title', 'Вълшебни срещи')
@section('meta_title', 'Събития и семинари за справяне със стреса | MindBloom')
@section('meta_description', 'Предстоящи приказни събития, семинари за справяне със стреса и групови срещи за подкрепа във Варна. Вижте кога е следващата ни среща.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/events-index-banner.jpg') }}'); background-position:35% center;">
    <div class="container wide">
      <h1 class="mad-page-title">Вълшебни срещи</h1>
      <h2 class="mad-page-subtitle">Предстоящи приказни събития и семинари за стрес</h2>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Вълшебни срещи</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="mad-entities mad-text-medium with-date type-2">
        @foreach ($events as $event)
          <div class="mad-col">
            <article class="mad-entity">
              @if ($event->image)
                <div class="mad-entity-media">
                  <a href="{{ route('events.show', $event) }}"><img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" /></a>
                </div>
              @endif
              <div class="mad-entity-content">
                <div class="mad-entity-date">
                  <span>{{ $event->starts_at?->format('d') }}</span>
                  <span>{{ $event->starts_at?->translatedFormat('M') }}</span>
                </div>
                <div class="mad-entity-header">
                  <div class="mad-entity-tags">
                    @if ($event->location)
                      <span>{{ $event->location }}</span>
                    @endif
                  </div>
                  <h4 class="mad-entity-title">
                    <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                  </h4>
                  <p>{{ $event->excerpt }}</p>
                  <div class="mad-entity-footer">
                    <a href="{{ route('events.show', $event) }}" class="mad-text-link">Прочети още</a>
                  </div>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if ($events->isEmpty())
        <p class="text-center">В момента няма предстоящи събития — проверете отново скоро.</p>
      @endif

      {{ $events->links() }}
    </div>
  </div>
@endsection
