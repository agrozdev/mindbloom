@php
  $isPost = $order->orderable instanceof \App\Models\Post;
@endphp
@extends('emails.layout')

@section('subject', 'Ново плащане получено')
@section('heading', 'Ново плащане получено')

@section('content')
  <p style="margin:0 0 0.75rem;"><strong>{{ $isPost ? 'История' : 'Събитие' }}:</strong> {{ $order->orderable->title }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Сума:</strong> {{ number_format((float) $order->amount, 2) }} {{ $order->currency }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Име:</strong> {{ $order->name }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Имейл:</strong> {{ $order->email }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Телефон:</strong> {{ $order->phone }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Номер на поръчка:</strong> {{ $order->uuid }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Дата на плащане:</strong> {{ $order->paid_at?->translatedFormat('j F Y, H:i') }} ч.</p>
@endsection
