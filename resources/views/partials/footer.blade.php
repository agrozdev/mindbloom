<footer id="mad-footer" class="mad-footer">
  <div class="container-fluid">
    <div class="mad-footer-main">
      <div class="row justify-content-between vr-size-1 hr-size-1">
        <div class="col-xl-4 col-lg-6 col-sm-12">
          <section class="mad-widget">
            <a href="{{ route('home') }}" class="mad-logo content-element-4"><img src="{{ asset('images/logo-mindbloom-dark.png') }}" alt="MindBloom" /></a>
            <div class="content-element-4">
              <p>MindBloom е психотерапевтична практика с индивидуална, групова терапия и уъркшопи, в топла и
                поверителна обстановка.</p>
            </div>
            <p class="copyrights">Всички права запазени &copy; {{ now()->year }} <a href="{{ route('home') }}">MindBloom</a>.</p>
          </section>
        </div>
        <div class="col-xl-2 col-lg-3 col-sm-6">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Меню</h6>
            <ul class="mad-vr-list">
              <li><a href="{{ route('about') }}">Моят поглед</a></li>
              <li><a href="{{ route('services.index') }}">Практика</a></li>
              <li><a href="{{ route('events.index') }}">Вълшебни срещи</a></li>
              <li><a href="{{ route('blog.index') }}">Вдъхновяващи истории</a></li>
              <li><a href="{{ route('contact') }}">Контакти</a></li>
            </ul>
          </section>
        </div>
        <div class="col-xl-2 col-lg-3 col-sm-6">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Практика</h6>
            <ul class="mad-vr-list">
              @foreach (\App\Models\Service::active()->limit(5)->get() as $footerService)
                <li><a href="{{ route('services.show', $footerService) }}">{{ $footerService->title }}</a></li>
              @endforeach
            </ul>
          </section>
        </div>
        <div class="col-xl-4 col-lg-12">
          <section class="mad-widget">
            <h6 class="mad-widget-title">Абонирайте се за новини</h6>
            <p class="content-element-2">Получавайте новини и специални оферти</p>
            <form class="mad-newsletter-form one-line">
              <div class="mad-col">
                <input type="email" name="email" placeholder="Въведете имейл адрес" />
              </div>
              <div class="mad-col">
                <button type="submit" class="btn btn-big btn-style-3">Абонирай се</button>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </div>
</footer>
