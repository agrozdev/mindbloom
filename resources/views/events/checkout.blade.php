@extends('layouts.app')

@section('title', 'Регистрация за '.$event->title)
@section('meta_description', 'Регистрация и плащане за '.$event->title)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Регистрация за събитие</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('events.index') }}" class="mad-link">Вълшебни срещи</a></span> /
        <span><a href="{{ route('events.show', $event) }}" class="mad-link">{{ $event->title }}</a></span> /
        <span>Регистрация</span>
      </nav>
    </div>
  </div>

  <div class="mad-content no-pd">
    <div class="container-fluid">
      <div class="mad-section">
        <div class="row vr-size-1">
          <div class="col-lg-5">
            <h2 class="mad-title">Обобщение на поръчката</h2>
            <ul class="mad-vr-list content-element-3">
              <li><strong>{{ $event->title }}</strong> — {{ number_format((float) $event->price, 2) }} €</li>
              @if ($event->starts_at)
                <li><strong>Кога:</strong> {{ $event->starts_at->translatedFormat('j F Y, H:i') }} ч.</li>
              @endif
              @if ($event->location)
                <li><strong>Къде:</strong> {{ $event->location }}</li>
              @endif
            </ul>
            <p class="mad-text-medium content-element-3">
              <strong>Общо за плащане: {{ number_format((float) $event->price, 2) }} €</strong>
            </p>
          </div>
          <div class="col-lg-7">
            <h2 class="mad-title">Данни за регистрация</h2>

            @if (session('error'))
              <div class="mad-alert-box mad-alert-box--error content-element-3">
                <div class="mad-alert-box-inner">{{ session('error') }}</div>
              </div>
            @endif

            <form id="mad-event-checkout-form" class="mad-contact-form mad-form type-2 item-col-2" method="POST" action="{{ route('events.register.store', $event) }}" novalidate data-validate>
              @csrf
              <div class="mad-form-honeypot" aria-hidden="true">
                <label for="checkout_website">Website</label>
                <input type="text" id="checkout_website" name="website" tabindex="-1" autocomplete="off" />
              </div>
              <input type="hidden" name="form_rendered_at" value="{{ now()->timestamp }}" />
              <div class="mad-col">
                <div class="mad-form-item @error('name') has-error @enderror">
                  <label>Име *</label>
                  <input type="text" name="name" value="{{ old('name') }}" required data-error="Моля, въведете вашето име." placeholder="Вашето име" />
                  @error('name') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item @error('email') has-error @enderror">
                  <label>Имейл *</label>
                  <input type="email" name="email" value="{{ old('email') }}" required data-error="Моля, въведете валиден имейл адрес." placeholder="Имейл адрес" />
                  @error('email') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item @error('phone') has-error @enderror">
                  <label>Телефон *</label>
                  <input type="tel" name="phone" value="{{ old('phone') }}" required data-error="Моля, въведете телефонен номер." placeholder="Телефонен номер" />
                  @error('phone') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item">
                  <button type="submit" class="btn btn-big">
                    <span>Продължи към плащане</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
