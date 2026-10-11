@extends('emails.layout')

@section('subject', 'Нов коментар към историята "' . $comment->post->title . '"')
@section('heading', 'Нов коментар чака одобрение')

@section('content')
  <p style="margin:0 0 0.75rem;"><strong>История:</strong> {{ $comment->post->title }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Име:</strong> {{ $comment->name }}</p>
  <p style="margin:0 0 0.75rem;"><strong>Имейл:</strong> {{ $comment->email }}</p>
  <p style="margin:1.25rem 0 0.5rem;"><strong>Коментар:</strong></p>
  <p style="margin:0 0 1.25rem; white-space:pre-line;">{{ $comment->body }}</p>
  <p style="margin:0;">
    <a href="{{ route('filament.admin.resources.comments.index') }}">Прегледай и одобри коментара</a>
  </p>
@endsection
