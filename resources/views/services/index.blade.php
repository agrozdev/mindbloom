@extends('layouts.app')

@section('title', 'Дом на промяната')
@section('meta_description', 'Сесия за лична яснота, сила в споделеното и уъркшопи в практиката на MindBloom.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Дом на промяната</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Дом на промяната</span>
      </nav>
    </div>
  </div>

  <div class="mad-content no-pd">
    <div class="container-fluid">
      <div class="mad-section">
        <div class="mad-icon-boxes grid-type item-col-4">
          @foreach ($services as $service)
            <div class="mad-col">
              <a href="{{ route('services.show', $service) }}" class="mad-icon-box">
                <div class="mad-icon-box-content">
                  @if ($service->icon)
                    <i class="mad-icon-box-icon"><img class="svg" src="{{ asset($service->icon) }}" alt="" /></i>
                  @endif
                  <h6 class="mad-icon-box-title">{{ $service->title }}</h6>
                  <p>{{ $service->excerpt }}</p>
                  <div class="mad-text-link">Прочети още</div>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        @if ($services->isEmpty())
          <p class="text-center">Все още няма публикувани услуги.</p>
        @endif
      </div>
    </div>
  </div>
@endsection
