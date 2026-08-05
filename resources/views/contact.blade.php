@extends('layouts.app')

@section('title', 'Време за вас')
@section('meta_description', 'Свържете се с MindBloom за безплатна консултация.')

@section('content')
  <div class="mad-breadcrumb with-bg-img with-overlay" style="background-image:url('{{ asset('images/1920x512_bg3.jpg') }}')">
    <div class="container wide">
      <h1 class="mad-page-title">Време за вас</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Време за вас</span>
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
                <span>0897 416 375</span>
              </li>
              <li>
                <img src="{{ asset('psychologist_icons_svg/email.svg') }}" alt="" class="svg" />
                <a href="mailto:info@mindbloombg.com" class="mad-link">info@mindbloombg.com</a>
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
              <div class="mad-alert-box mad-alert-box--success content-element-3">
                <div class="mad-alert-box-inner">{{ session('status') }}</div>
              </div>
            @endif

            <form id="mad-contact-form" class="mad-contact-form mad-form type-2 item-col-2" method="POST" action="{{ route('contact.store') }}" novalidate data-validate>
              @csrf
              <div class="mad-form-honeypot" aria-hidden="true">
                <label for="contact_website">Website</label>
                <input type="text" id="contact_website" name="website" tabindex="-1" autocomplete="off" />
              </div>
              <input type="hidden" name="form_rendered_at" value="{{ now()->timestamp }}" />
              <div class="mad-col">
                <div class="mad-form-item @error('first_name') has-error @enderror">
                  <label>Име *</label>
                  <input type="text" name="first_name" value="{{ old('first_name') }}" required data-error="Моля, въведете вашето име." placeholder="Име" />
                  @error('first_name') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item @error('last_name') has-error @enderror">
                  <label>Фамилия *</label>
                  <input type="text" name="last_name" value="{{ old('last_name') }}" required data-error="Моля, въведете вашата фамилия." placeholder="Фамилия" />
                  @error('last_name') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item @error('email') has-error @enderror">
                  <label>Имейл *</label>
                  <input type="email" name="email" value="{{ old('email') }}" required data-error="Моля, въведете валиден имейл адрес." placeholder="Имейл адрес" />
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
                <div class="mad-form-item full-height @error('message') has-error @enderror">
                  <label>Съобщение *</label>
                  <textarea rows="5" name="message" required data-error="Моля, въведете съобщение." placeholder="Съобщение">{{ old('message') }}</textarea>
                  @error('message') <span class="mad-error-message">{{ $message }}</span> @enderror
                </div>
                <div class="mad-form-item @error('agree_terms') has-error @enderror">
                  <input type="checkbox" id="agree_terms" name="agree_terms" value="1" required data-error="Моля, потвърдете съгласието си с Политиката на поверителност и Общите условия." @checked(old('agree_terms')) />
                  <label for="agree_terms">
                    Съгласен/а съм с <a href="{{ route('legal.privacy') }}" target="_blank">Политиката на поверителност</a>
                    и <a href="{{ route('legal.terms') }}" target="_blank">Общите условия за ползване</a>. *
                  </label>
                  @error('agree_terms') <span class="mad-error-message">{{ $message }}</span> @enderror
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
