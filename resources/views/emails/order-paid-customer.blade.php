@php
  $isPost = $order->orderable instanceof \App\Models\Post;
@endphp
@extends('emails.layout')

@section('subject', 'Потвърждение за плащане')
@section('heading', $isPost ? 'Историята е отключена!' : 'Благодарим ви за регистрацията!')

@section('content')
  <p style="margin:0 0 0.75rem;">Здравейте, {{ $order->name }},</p>
  <p style="margin:0 0 1.25rem;">Плащането ви беше успешно обработено. Ето детайли за {{ $isPost ? 'поръчката' : 'регистрацията' }}:</p>
  <p style="margin:0 0 0.75rem;"><strong>{{ $isPost ? 'История' : 'Събитие' }}:</strong> {{ $order->orderable->title }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Сума:</strong> {{ number_format((float) $order->amount, 2) }} {{ $order->currency }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Номер на поръчка:</strong> {{ $order->uuid }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Дата на плащане:</strong> {{ $order->paid_at?->translatedFormat('j F Y, H:i') }} ч.</p>
  @if ($isPost)
    <p style="margin:1.25rem 0 0.75rem;">
      <a href="{{ route('blog.show', [$order->orderable->category, $order->orderable, 'unlock' => $order->uuid]) }}">Прочети историята сега</a>
    </p>
    <p style="margin:0; font-size:0.8125rem; color:#707781;">Запазете този имейл — линкът по-горе е персоналният ви достъп до пълния текст на историята.</p>
  @else
    <p style="margin:1.25rem 0 0;">Ще се видим скоро!</p>
  @endif
@endsection
