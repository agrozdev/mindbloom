@extends('layouts.app')

@section('title', 'Плащането беше отказано')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}'); background-position:15% center;">
    <div class="container wide">
      <h1 class="mad-page-title">Плащането беше отказано</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Плащането беше отказано</span>
      </nav>
    </div>
  </div>

  @php
    $isPost = $order->orderable instanceof \App\Models\Post;
    $backUrl = $isPost ? route('blog.show', [$order->orderable->category, $order->orderable]) : route('events.show', $order->orderable);
    $backLabel = $isPost ? 'Обратно към историята' : 'Обратно към събитието';
  @endphp

  <div class="mad-content">
    <div class="container">
      <div class="mad-alert-box mad-alert-box--warning content-element-3">
        <div class="mad-alert-box-inner">Плащането не беше завършено. Не сте таксувани и {{ $isPost ? 'историята остава заключена' : 'не сте регистрирани за събитието' }}.</div>
      </div>

      <a href="{{ $backUrl }}" class="btn btn-big">{{ $backLabel }}</a>
    </div>
  </div>
@endsection
