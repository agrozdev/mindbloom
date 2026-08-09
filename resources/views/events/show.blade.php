@extends('layouts.app')

@section('title', $event->title)
@section('meta_description', $event->excerpt)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $event->title }}</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('events.index') }}" class="mad-link">Вълшебни срещи</a></span> /
        <span>{{ $event->title }}</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          @if ($event->image)
            <div class="mad-entity-media content-element-4">
              <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" />
            </div>
          @endif
          <ul class="mad-vr-list content-element-3">
            @if ($event->starts_at)
              <li><strong>Кога:</strong> {{ $event->starts_at->translatedFormat('j F Y, H:i') }} ч.</li>
            @endif
            @if ($event->location)
              <li><strong>Къде:</strong> {{ $event->location }}</li>
            @endif
            @if ($event->price !== null)
              <li><strong>Цена:</strong> {{ number_format((float) $event->price, 2) }} €</li>
            @endif
          </ul>
          <div class="mad-text-medium">
            {!! $event->description !!}
          </div>
        </div>
        <div class="col-lg-4">
          <h6 class="mad-widget-title">Други събития</h6>
          <ul class="mad-vr-list content-element-3">
            @foreach (\App\Models\Event::active()->where('id', '!=', $event->id)->limit(6)->get() as $other)
              <li><a href="{{ route('events.show', $other) }}">{{ $other->title }}</a></li>
            @endforeach
          </ul>
          <a href="{{ route('events.register', $event) }}" class="btn btn-big">Регистрирай се за събитието</a>
        </div>
      </div>
    </div>
  </div>
@endsection
