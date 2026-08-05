@extends('emails.layout')

@section('subject', 'Въпрос към историята "' . $post->title . '"')
@section('heading', 'Нов въпрос към история')

@section('content')
  <p style="margin:0 0 0.75rem;"><strong>История:</strong> {{ $post->title }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Име:</strong> {{ $data['name'] }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Имейл:</strong> {{ $data['email'] }}</p>
  @if (!empty($data['phone']))
    <p style="margin:0 0 0.75rem;"><strong>Телефон:</strong> {{ $data['phone'] }}</p>
  @endif
  <p style="margin:1.25rem 0 0.5rem;"><strong>Въпрос:</strong></p>
  <p style="margin:0; white-space:pre-line;">{{ $data['question'] }}</p>
@endsection
