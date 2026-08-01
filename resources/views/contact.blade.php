@extends('layouts.app')

@section('title', 'Среща с мен')
@section('meta_description', 'Свържете се с MindBloom за безплатна консултация.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Среща с мен</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Среща с мен</span>
      </nav>
    </div>
  </div>

  <div class="mad-content no-pd">
    <div class="container-fluid">
      <div class="mad-section">
        <div class="row vr-size-1">
          <div class="col-lg-6">
            <h2 class="mad-title">Данни за контакт</h2>
            <p class="mad-text-medium content-element-4">Не се колебайте да се свържете с нас при въпроси.</p>
            <ul class="mad-vr-list mad-map-info content-element-3">
              <li>
                <img src="{{ asset('psychologist_icons_svg/loc4.svg') }}" alt="" class="svg" />
                <span>Варна, България</span>
              </li>
              <li>
                <img src="{{ asset('psychologist_icons_svg/phone4.svg') }}" alt="" class="svg" />
                <span>+359 800 000 000</span>
              </li>
              <li>
                <img src="{{ asset('psychologist_icons_svg/email.svg') }}" alt="" class="svg" />
                <a href="mailto:hello@mindbloombg.com" class="mad-link">hello@mindbloombg.com</a>
              </li>
              <li>
                <img src="{{ asset('psychologist_icons_svg/clock4.svg') }}" alt="" class="svg" />
                Понеделник - Петък: 9:00 - 18:00
              </li>
            </ul>
          </div>
          <div class="col-lg-6">
            <h2 class="mad-title">Пишете ни</h2>
            <p class="mad-text-medium content-element-4">Полетата, отбелязани с *, са задължителни.</p>

            @if (session('status'))
              <div class="mad-alert-box success content-element-3">{{ session('status') }}</div>
            @endif

            <form class="mad-contact-form mad-form type-2 item-col-2" method="POST" action="{{ route('contact.store') }}">
              @csrf
              <div class="mad-col">
                <div class="mad-form-item">
                  <label>Име *</label>
                  <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Име" />
                  @error('first_name') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item">
                  <label>Фамилия *</label>
                  <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Фамилия" />
                  @error('last_name') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item">
                  <label>Имейл *</label>
                  <input type="email" name="email" value="{{ old('email') }}" required placeholder="Имейл адрес" />
                  @error('email') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item">
                  <label>Телефон</label>
                  <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Телефонен номер" />
                </div>
              </div>
              <div class="mad-col">
                <div class="mad-form-item">
                  <label>Интерес към</label>
                  <div class="mad-custom-select">
                    <select data-default-text="Интерес към" name="service">
                      <option value="">Все още не съм сигурен/а</option>
                      @foreach (\App\Models\Service::active()->get() as $service)
                        <option value="{{ $service->title }}" @selected(old('service') === $service->title)>{{ $service->title }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="mad-form-item full-height">
                  <label>Съобщение *</label>
                  <textarea rows="5" name="message" required placeholder="Съобщение">{{ old('message') }}</textarea>
                  @error('message') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item">
                  <button type="submit" class="btn btn-big">
                    <span>Изпрати</span>
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
