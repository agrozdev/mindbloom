@extends('layouts.app')

@section('title', $service->title)
@section('meta_description', $service->excerpt)

@php
  $serviceSchemaMeta = [
    'individualna-terapiya' => [
      'serviceType' => 'Individual psychotherapy session',
      'description' => 'Индивидуална терапевтична сесия в практиката на MindBloom, Варна.',
    ],
    'grupova-terapiya' => [
      'serviceType' => 'Group therapy session',
      'description' => 'Групова терапевтична сесия в практиката на MindBloom, Варна.',
    ],
    'uyrkshopi' => [
      'serviceType' => 'Workshop',
      'description' => 'Уъркшоп в практиката на MindBloom, Варна.',
    ],
  ][$service->slug] ?? [
    'serviceType' => 'Service',
    'description' => $service->excerpt,
  ];

  $serviceSeoMeta = [
    'individualna-terapiya' => [
      'meta_title' => 'Индивидуална приказкотерапия за самоосъзнаване | MindBloom',
      'meta_description' => 'Индивидуални сесии за самоосъзнаване чрез приказки във Варна и онлайн. Открийте вътрешна яснота и баланс в спокойно, безопасно пространство.',
      'h2' => 'Индивидуални сесии за самоосъзнаване чрез приказки',
      'image_alt' => 'Индивидуална приказкотерапия за самоосъзнаване',
    ],
    'grupova-terapiya' => [
      'meta_title' => 'Групова терапия при стрес чрез приказки | MindBloom',
      'meta_description' => 'Групови срещи за преодоляване на стрес чрез приказки — споделено пространство за спокойствие, увереност и подкрепа във Варна и онлайн.',
      'h2' => 'Групови срещи за преодоляване на стрес чрез приказки',
      'image_alt' => 'Групова приказкотерапия за справяне със стрес',
    ],
    'uyrkshopi' => [
      'meta_title' => 'Уъркшопи с терапевтични приказки за развитие | MindBloom',
      'meta_description' => 'Терапевтични уъркшопи с приказки за личностно развитие и вътрешна промяна. Тиха среща със себе си във Варна и онлайн — направете първата крачка.',
      'h2' => 'Уъркшопи с терапевтични приказки за личностно развитие',
      'image_alt' => 'Терапевтичен уъркшоп с приказки за личностно развитие',
    ],
  ][$service->slug] ?? null;
@endphp

@if ($serviceSeoMeta)
  @section('meta_title', $serviceSeoMeta['meta_title'])
  @section('meta_description', $serviceSeoMeta['meta_description'])
@endif

@push('scripts')
  <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Service",
      "serviceType": {!! json_encode($serviceSchemaMeta['serviceType']) !!},
      "name": {!! json_encode($service->title) !!},
      "description": {!! json_encode($serviceSchemaMeta['description']) !!},
      "url": {!! json_encode(route('services.show', $service)) !!},
      "provider": { "@@id": {!! json_encode(route('home') . '#business') !!} },
      "areaServed": { "@@type": "City", "name": "Varna" }
    }
  </script>
@endpush

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $service->title }}</h1>
      @if ($serviceSeoMeta)
        <h2 class="mad-page-subtitle">{{ $serviceSeoMeta['h2'] }}</h2>
      @endif
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('services.index') }}" class="mad-link">Нова посока</a></span> /
        <span>{{ $service->title }}</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          @if ($service->image)
            <div class="mad-entity-media content-element-4">
              <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $serviceSeoMeta['image_alt'] ?? $service->title }}" />
            </div>
          @endif
          <div class="mad-text-medium" style="font-family:'Marck Script', cursive;">
            {!! $service->description !!}
          </div>
        </div>
        <div class="col-lg-4">
          <h6 class="mad-widget-title">Друго от практиката</h6>
          <ul class="mad-vr-list">
            @foreach (\App\Models\Service::active()->where('id', '!=', $service->id)->limit(6)->get() as $other)
              <li><a href="{{ route('services.show', $other) }}">{{ $other->title }}</a></li>
            @endforeach
          </ul>
          <a href="{{ route('contact') }}" class="btn btn-big content-element-4">Запази своето място</a>
        </div>
      </div>
    </div>
  </div>
@endsection
