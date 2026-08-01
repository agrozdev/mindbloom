<div class="mad-d-none">
  <div id="search-modal" class="mad-modal mad-modal--search">
    <h6 class="mad-title">Търсене</h6>
    <form class="mad-search-section">
      <input type="text" placeholder="Започнете да пишете тук..." />
      <button type="submit" class="btn"><i class="material-icons">search</i><span>търсене</span></button>
    </form>
    <button type="button" class="arcticmodal-close">
      <i class="material-icons">close</i>
    </button>
  </div>
</div>

<header id="mad-header" class="mad-header">
  <div class="mad-pre-header">
    <div class="container-fluid">
      <div class="mad-header-items">
        <div class="mad-header-item">
          <p>Приемаме нови клиенти <a href="{{ route('contact') }}" class="mad-ulink">Запишете безплатна консултация</a></p>
        </div>
        <div class="mad-header-item">
          <div class="mad-our-info">
            <div class="mad-our-info">
              <div class="mad-info">
                <i><img src="{{ asset('psychologist_icons_svg/loc3.svg') }}" alt="" class="svg" /></i>
                <span>София, България</span>
              </div>
              <div class="mad-info">
                <i><img src="{{ asset('psychologist_icons_svg/phone3.svg') }}" alt="" class="svg" /></i>
                <span>+359 800 000 000</span>
              </div>
            </div>
            <div class="mad-info">
              <div class="mad-social-icons size-small">
                <ul>
                  <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                  <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                  <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="mad-header-section--sticky-xl">
    <div class="container-fluid">
      <div class="mad-header-items">
        <div class="mad-header-item">
          <a href="{{ route('home') }}" class="mad-logo"><img src="{{ asset('images/logo-mindbloom.png') }}" alt="MindBloom" /></a>
        </div>
        <div class="mad-header-item">
          <nav class="mad-navigation-container">
            <ul class="mad-navigation mad-navigation--vertical-sm">
              <li class="menu-item {{ request()->routeIs('home') ? 'current-menu-item' : '' }}">
                <a href="{{ route('home') }}">Начало</a>
              </li>
              <li class="menu-item {{ request()->routeIs('about') ? 'current-menu-item' : '' }}">
                <a href="{{ route('about') }}">За мен</a>
              </li>
              <li class="menu-item {{ request()->routeIs('services.*') ? 'current-menu-item' : '' }}">
                <a href="{{ route('services.index') }}">Практика</a>
              </li>
              <li class="menu-item {{ request()->routeIs('events.*') ? 'current-menu-item' : '' }}">
                <a href="{{ route('events.index') }}">Събития</a>
              </li>
              <li class="menu-item {{ request()->routeIs('blog.*') ? 'current-menu-item' : '' }}">
                <a href="{{ route('blog.index') }}">Новини</a>
              </li>
              <li class="menu-item {{ request()->routeIs('contact') ? 'current-menu-item' : '' }}">
                <a href="{{ route('contact') }}">Контакти</a>
              </li>
            </ul>
          </nav>
          <div class="mad-actions">
            <div class="mad-col">
              <a href="#" data-arctic-modal="#search-modal"><i class="material-icons">search</i></a>
            </div>
            <div class="mad-col">
              <a href="{{ route('contact') }}" class="btn">БЕЗПЛАТНА КОНСУЛТАЦИЯ</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
