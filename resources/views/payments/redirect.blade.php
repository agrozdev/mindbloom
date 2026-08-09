@extends('layouts.app')

@section('title', 'Пренасочване към плащане')

@section('content')
  <div class="mad-content">
    <div class="container">
      <div class="text-center" style="padding:4rem 0;">
        <h2 class="mad-title">Пренасочваме ви към страницата за плащане…</h2>
        <p class="mad-text-medium content-element-3">Моля, изчакайте. Ще бъдете отведени автоматично до myPOS.</p>

        <form id="mypos-redirect-form" method="POST" action="{{ $actionUrl }}">
          @foreach ($formData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
          @endforeach
          <button type="submit" class="btn btn-big">Продължи ръчно</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('mypos-redirect-form').submit();
  </script>
@endsection
