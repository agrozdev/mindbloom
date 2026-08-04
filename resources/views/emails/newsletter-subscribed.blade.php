@extends('emails.layout')

@section('subject', 'Нов абонат за бюлетина')
@section('heading', 'Нов абонат за бюлетина')

@section('content')
  <p style="margin:0;">Нов посетител се абонира за бюлетина на MindBloom с имейл:</p>
  <p style="margin:0.75rem 0 0;"><strong>{{ $email }}</strong></p>
@endsection
