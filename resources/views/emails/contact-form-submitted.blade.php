@extends('emails.layout')

@section('subject', 'Ново запитване от контактната форма')
@section('heading', 'Ново запитване от контактната форма')

@section('content')
  <p style="margin:0 0 0.75rem;"><strong>Име:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Имейл:</strong> {{ $data['email'] }}</p>
  @if (!empty($data['phone']))
    <p style="margin:0 0 0.75rem;"><strong>Телефон:</strong> {{ $data['phone'] }}</p>
  @endif
  @if (!empty($data['service']))
    <p style="margin:0 0 0.75rem;"><strong>Интерес към:</strong> {{ $data['service'] }}</p>
  @endif
  <p style="margin:1.25rem 0 0.5rem;"><strong>Съобщение:</strong></p>
  <p style="margin:0; white-space:pre-line;">{{ $data['message'] }}</p>
@endsection
