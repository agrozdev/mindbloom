@extends('layouts.app')

@section('title', 'Благодарим ви')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}'); background-position:15% center;">
    <div class="container wide">
      <h1 class="mad-page-title">Благодарим ви</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Благодарим ви</span>
      </nav>
    </div>
  </div>

  @php
    $isPost = $order->orderable instanceof \App\Models\Post;
    $itemLabel = $isPost ? 'История' : 'Събитие';
    $backUrl = $isPost
      ? route('blog.show', [$order->orderable->category, $order->orderable, 'unlock' => $order->uuid])
      : route('events.show', $order->orderable);
    $backLabel = $isPost ? 'Прочети историята' : 'Обратно към събитието';
  @endphp

  <div class="mad-content">
    <div class="container">
      @if ($order->status === \App\Models\Order::STATUS_PAID)
        <div class="mad-alert-box mad-alert-box--success content-element-3">
          <div class="mad-alert-box-inner">Плащането е успешно! {{ $isPost ? 'Отключихте историята' : 'Регистрирахте се за' }} «{{ $order->orderable->title }}».</div>
        </div>
      @else
        <div class="mad-alert-box mad-alert-box--warning content-element-3">
          <div class="mad-alert-box-inner">Обработваме плащането ви. Ще получите потвърждение по имейл веднага щом то приключи.</div>
        </div>
      @endif

      <ul class="mad-vr-list content-element-3">
        <li><strong>{{ $itemLabel }}:</strong> {{ $order->orderable->title }}</li>
        <li><strong>Сума:</strong> {{ number_format((float) $order->amount, 2) }} {{ $order->currency }}</li>
        <li><strong>Номер на поръчка:</strong> {{ $order->uuid }}</li>
        <li><strong>Име:</strong> {{ $order->name }}</li>
        <li><strong>Имейл:</strong> {{ $order->email }}</li>
      </ul>

      <a href="{{ $backUrl }}" class="btn btn-big">{{ $backLabel }}</a>
    </div>
  </div>
@endsection
