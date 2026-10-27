@extends('readers.layout')

@section('title', 'Отписване')
@section('page_heading', 'Отписване')

@section('page_body')
  @if (! $reader)
    <p>Връзката е невалидна или изтекла.</p>
  @elseif ($done)
    <div class="mad-alert-box mad-alert-box--success content-element-3" role="status">
      <div class="mad-alert-box-inner">Успешно се отписахте. Няма да получавате повече имейли от нас.</div>
    </div>
    <p><a href="{{ route('blog.index') }}" class="btn">Към приказките</a></p>
  @else
    <h2 class="content-element-3" style="font-size:1.5rem;">Искате ли да се отпишете?</h2>
    <p class="content-element-3">Няма да получавате повече имейли от MindBloom.</p>
    <form method="POST" action="{{ route('unsubscribe.store', $token) }}">
      <button type="submit" class="btn btn-big">Отпиши ме</button>
    </form>
  @endif
@endsection
