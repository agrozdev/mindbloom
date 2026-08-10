@extends('layouts.app')

@section('title', 'Отключване на '.$post->title)
@section('meta_description', 'Плащане за отключване на историята '.$post->title)

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg4.jpg') }}'); background-position:15% center;">
    <div class="container wide">
      <h1 class="mad-page-title">Отключване на история</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> /
        <span><a href="{{ route('blog.index') }}" class="mad-link">Вдъхновяващи истории</a></span> /
        <span><a href="{{ route('blog.show', [$post->category, $post]) }}" class="mad-link">{{ $post->title }}</a></span> /
        <span>Отключване</span>
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
              <li><strong>{{ $post->title }}</strong> — {{ number_format((float) $post->price, 2) }} €</li>
            </ul>
            <p class="mad-text-medium content-element-3">
              <strong>Общо за плащане: {{ number_format((float) $post->price, 2) }} €</strong>
            </p>
          </div>
          <div class="col-lg-7">
            <h2 class="mad-title">Данни за поръчката</h2>

            @if (session('error'))
              <div class="mad-alert-box mad-alert-box--error content-element-3">
                <div class="mad-alert-box-inner">{{ session('error') }}</div>
              </div>
            @endif

            <form id="mad-post-checkout-form" class="mad-contact-form mad-form type-2 item-col-2" method="POST" action="{{ route('blog.unlock.store', [$post->category, $post]) }}" novalidate data-validate>
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
