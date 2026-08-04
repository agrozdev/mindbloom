<footer id="mad-footer" class="mad-footer">
  <div class="container-fluid">
    <div class="mad-footer-main">
      <div class="row justify-content-between vr-size-1 hr-size-1">
        <div class="col-xl-4 col-lg-6 col-sm-12">
          <section class="mad-widget">
            <a href="{{ route('home') }}" class="mad-logo content-element-4"><img src="{{ asset('images/logo-mindbloom.png') }}" alt="MindBloom" /></a>
            <div class="content-element-4">
              <p>Място, където умът се успокоява, сърцето си спомня, това което душата отдавна знае, а вътрешната
                промяна започва с нежно и истинско осъзнаване.<br /><br />
                Ексклузивно пространство за вътрешен баланс, яснота и личностно израстване. Деликатен процес на
                промяна, в който срещаш себе си по-нов, по-сигурен и дълбок начин.</p>
            </div>
            <p class="copyrights">Всички права запазени &copy; {{ now()->year }} <a href="{{ route('home') }}">MindBloom</a>. Crafted by GrozdevDigital.</p>
          </section>
        </div>
        <div class="col-xl-2 col-lg-3 col-sm-6">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Меню</h6>
            <ul class="mad-vr-list">
              <li><a href="{{ route('about') }}">Моят поглед</a></li>
              <li><a href="{{ route('services.index') }}">Нова посока</a></li>
              <li><a href="{{ route('events.index') }}">Вълшебни срещи</a></li>
              <li><a href="{{ route('blog.index') }}">Вдъхновяващи истории</a></li>
              <li><a href="{{ route('contact') }}">Време за вас</a></li>
            </ul>
          </section>
        </div>
        <div class="col-xl-2 col-lg-3 col-sm-6">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Нова посока</h6>
            <ul class="mad-vr-list">
              @foreach (\App\Models\Service::active()->limit(5)->get() as $footerService)
                <li><a href="{{ route('services.show', $footerService) }}">{{ $footerService->title }}</a></li>
              @endforeach
            </ul>
          </section>
        </div>
        <div class="col-xl-4 col-lg-12">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Присъедини се към кръга на вдъхновението</h6>
            <p class="content-element-2">Получавайте новини и специални оферти</p>

            @if (session('newsletter_status'))
              <div class="mad-alert-box mad-alert-box--success content-element-3">
                <div class="mad-alert-box-inner">{{ session('newsletter_status') }}</div>
              </div>
            @endif

            <form class="mad-newsletter-form one-line" method="POST" action="{{ route('newsletter.store') }}">
              @csrf
              <div class="mad-form-honeypot" aria-hidden="true">
                <label for="newsletter_website">Website</label>
                <input type="text" id="newsletter_website" name="website" tabindex="-1" autocomplete="off" />
              </div>
              <input type="hidden" name="form_rendered_at" value="{{ now()->timestamp }}" />
              <div class="mad-col">
                <input type="email" name="newsletter_email" value="{{ old('newsletter_email') }}" required placeholder="Въведете имейл адрес" />
                @error('newsletter_email') <span class="mad-error-message">{{ $message }}</span> @enderror
              </div>
              <div class="mad-col">
                <button type="submit" class="btn btn-big btn-style-3">Абонирай се</button>
              </div>
            </form>
            <ul class="mad-hr-list mad-legal-links">
              <li><a href="{{ route('legal.privacy') }}"><i class="material-icons">info</i>Политика на поверителност</a></li>
              <li><a href="{{ route('legal.terms') }}"><i class="material-icons">info</i>Общи условия</a></li>
              <li><a href="{{ route('legal.cookies') }}"><i class="material-icons">info</i>Политика за бисквитките</a></li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </div>
</footer>
