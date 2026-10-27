@extends('readers.layout')

@section('title', 'Невалидна връзка')
@section('page_heading', 'Невалидна връзка')

@section('page_body')
  <p>Връзката е невалидна или изтекла.</p>
  <p><a href="{{ route('blog.index') }}" class="btn">Към приказките</a></p>
@endsection
