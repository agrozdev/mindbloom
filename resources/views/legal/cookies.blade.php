@extends('layouts.app')

@section('title', 'Политика за бисквитките')
@section('meta_description', 'Политика за бисквитките на MindBloom — какви бисквитки използваме и как можете да управлявате съгласието си.')

@section('content')
  <div class="mad-breadcrumb">
    <div class="container wide">
      <h1 class="mad-page-title">Политика за бисквитките</h1>
      <nav class="mad-breadcrumb-path">
        <span><a href="{{ route('home') }}" class="mad-link">Начало</a></span> / <span>Политика за бисквитките</span>
      </nav>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div class="mad-text-medium mad-legal-content">
            <p><em>Последна актуализация: {{ now()->translatedFormat('j F Y') }}</em></p>

            <h4 class="mad-title">1. Какво представляват бисквитките</h4>
            <p>
              Бисквитките (cookies) са малки текстови файлове, които се съхраняват на Вашето
              устройство при посещение на уебсайт. Те позволяват на сайта да „запомни“ Вашите действия
              и предпочитания за определен период от време.
            </p>

            <h4 class="mad-title">2. Какви бисквитки използваме</h4>

            <h4 class="mad-title">2.1. Строго необходими бисквитки</h4>
            <p>
              Тези бисквитки са задължителни за нормалната работа на Сайта и не изискват съгласие.
              Без тях определени функции (напр. изпращане на форми) не биха работили правилно.
            </p>
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>Бисквитка</th>
                    <th>Цел</th>
                    <th>Срок</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>laravel_session</td>
                    <td>Поддържане на сесията на посетителя между заявките</td>
                    <td>При затваряне на браузъра / до 2 часа</td>
                  </tr>
                  <tr>
                    <td>XSRF-TOKEN</td>
                    <td>Защита срещу CSRF атаки при изпращане на форми</td>
                    <td>При затваряне на браузъра / до 2 часа</td>
                  </tr>
                  <tr>
                    <td>mb_cookie_consent</td>
                    <td>Запомня Вашия избор относно бисквитките (в локалното съхранение на браузъра)</td>
                    <td>До изтриване от потребителя</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <h4 class="mad-title">2.2. Аналитични бисквитки</h4>
            <p>
              Използваме ги, за да разберем как посетителите използват Сайта, с цел неговото
              подобряване. Задават се <strong>само след Вашето изрично съгласие</strong> чрез банера за
              бисквитки.
            </p>
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>Бисквитка</th>
                    <th>Доставчик</th>
                    <th>Цел</th>
                    <th>Срок</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>_ga, _ga_*, _gid</td>
                    <td>Google Analytics</td>
                    <td>Статистика за посещенията и поведението на сайта</td>
                    <td>До 2 години</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p>
              <em>Google Analytics в момента не е активен на Сайта. Тази таблица се прилага автоматично
              от момента, в който отчитането бъде включено.</em>
            </p>

            <h4 class="mad-title">3. Как да управлявате съгласието си</h4>
            <p>
              При първо посещение на Сайта ще видите банер, чрез който можете да изберете „Приемам
              всички“ или „Само необходими“. Можете да промените избора си по всяко време чрез бутона
              по-долу:
            </p>
            <p>
              <button type="button" id="mad-cookie-settings-btn" class="btn style-2">Промени настройките за бисквитки</button>
            </p>
            <p>
              Освен това повечето интернет браузъри позволяват управление на бисквитките чрез
              настройките си — включително изтриване на съществуващи бисквитки и блокиране на нови.
              Имайте предвид, че блокирането на необходимите бисквитки може да наруши функционирането
              на определени части от Сайта.
            </p>

            <h4 class="mad-title">4. Бисквитки на трети страни</h4>
            <p>
              Когато е активирана, Google Analytics поставя собствени бисквитки съгласно политиката на
              Google за поверителност. Повече информация можете да намерите на
              <a href="https://policies.google.com/technologies/cookies" target="_blank" rel="noopener">policies.google.com/technologies/cookies</a>.
            </p>

            <h4 class="mad-title">5. Промени в тази политика</h4>
            <p>Можем периодично да актуализираме настоящата политика, включително при добавяне на нови инструменти за анализ. Актуалната версия винаги е достъпна на тази страница.</p>

            <h4 class="mad-title">6. Контакт</h4>
            <p>При въпроси относно използването на бисквитки, пишете ни на info@mindbloombg.com.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.getElementById('mad-cookie-settings-btn')?.addEventListener('click', function () {
        window.madReopenCookieConsent?.();
      });
    </script>
  @endpush
@endsection
