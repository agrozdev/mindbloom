@extends('layouts.app')

@section('title', $service->title)
@section('meta_description', $service->excerpt)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">{{ $service->title }}</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('services.index') }}" class="mad-link">Дом на промяната</a></span> /
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
              <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" />
            </div>
          @endif
          <div class="mad-text-medium">
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
