@php
  $post = $comment->post;
@endphp
@extends('emails.layout')

@section('subject', 'Твоят коментар вече е част от историята')
@section('heading', 'Твоят коментар вече е част от историята')

@section('content')
  <p style="margin:0 0 0.75rem;">Здравей, {{ $comment->name }},</p>
  <p style="margin:0 0 0.75rem;">
    Имаме една малка хубава новина: коментарът ти към историята
    <strong>„{{ $post->title }}“</strong> беше одобрен и вече свети на страницата ѝ,
    за да го прочетат и други читатели.
  </p>
  <p style="margin:0 0 0.75rem;">
    Благодарим ти от сърце! Всяка твоя дума е като мъничко фенерче за нас —
    показва ни, че историите ни намират пътя до някого, и ни дава сили да седнем
    и да напишем следващата.
  </p>
  <p style="margin:0 0 1.25rem;">
    А в нашата гора има още много истории, които чакат да бъдат прочетени.
    Ако някоя те докосне, кажи ни — ние четем всеки коментар.
  </p>
  <p style="margin:0 0 0.75rem;">
    <a href="{{ route('blog.show', [$post->category, $post]) }}#comments">Виж коментара си</a>
    &nbsp;·&nbsp;
    <a href="{{ route('blog.index') }}">Прочети още истории</a>
  </p>
  <p style="margin:1.25rem 0 0;">С благодарност,<br>екипът на MindBloom</p>
@endsection
