@extends('layouts.app')

@section('title', $event->title)
@section('meta_description', $event->metaDescription())

@push('schema')
  @php
    $eventUrl = route('events.show', $event);
    $eventNode = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $event->title,
        'description' => $event->metaDescription(),
        'url' => $eventUrl,
        'inLanguage' => 'bg',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'eventStatus' => 'https://schema.org/EventScheduled',
        'organizer' => ['@id' => route('home') . '#business'],
    ];
    if ($event->image) {
        $eventNode['image'] = asset('storage/' . $event->image);
    }
    if ($event->starts_at) {
        $eventNode['startDate'] = $event->starts_at->toIso8601String();
    }
    $eventNode['location'] = $event->location
        ? ['@type' => 'Place', 'name' => $event->location, 'address' => $event->location]
        : ['@type' => 'Place', 'name' => 'MindBloom, Варна', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Varna', 'addressCountry' => 'BG']];
    if ($event->price !== null) {
        $eventNode['offers'] = [
            '@type' => 'Offer',
            'price' => number_format((float) $event->price, 2, '.', ''),
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
            'url' => route('events.register', $event),
        ];
    }
  @endphp
  <script type="application/ld+json">
{!! json_encode($eventNode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
  </script>
  @include('partials.schema-breadcrumb', ['items' => [
      ['name' => 'Начало', 'url' => route('home')],
      ['name' => 'Вълшебни срещи', 'url' => route('events.index')],
      ['name' => $event->title],
  ]])
@endpush

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}'); background-position:15% center;">
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
