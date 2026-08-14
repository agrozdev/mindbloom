<div id="mad-nav-canvas" class="mad-nav-canvas full-screen">
  <button class="mad-nav-close"><img src="{{ asset('psychologist_icons_svg/close.svg') }}" alt="" class="svg" /></button>
  <a href="{{ route('home') }}" class="mad-logo"><img src="{{ asset('images/logo-mindbloom.png') }}" alt="MindBloom" /></a>
  <div class="mad-nav-inner">
    <nav class="mad-vr-nav">
      <ul>
        <li class="menu-item {{ request()->routeIs('home') ? 'current-menu-item' : '' }}">
          <a href="{{ route('home') }}">Начало</a>
        </li>
        <li class="menu-item {{ request()->routeIs('about') ? 'current-menu-item' : '' }}">
          <a href="{{ route('about') }}">Моят поглед</a>
        </li>
        <li class="menu-item {{ request()->routeIs('services.*') ? 'current-menu-item' : '' }}">
          <a href="{{ route('services.index') }}">Нова посока</a>
        </li>
        <li class="menu-item {{ request()->routeIs('events.*') ? 'current-menu-item' : '' }}">
          <a href="{{ route('events.index') }}">Вълшебни срещи</a>
        </li>
        <li class="menu-item {{ request()->routeIs('blog.*') ? 'current-menu-item' : '' }}">
          <a href="{{ route('blog.index') }}">Вдъхновяващи истории</a>
        </li>
        <li class="menu-item {{ request()->routeIs('contact') ? 'current-menu-item' : '' }}">
          <a href="{{ route('contact') }}">Време за вас</a>
        </li>
      </ul>
    </nav>
    <div class="mad-icon-boxes with-border style-2 align-center small-size item-col-4">
      <div class="mad-col">
        <article class="mad-icon-box">
          <i class="mad-icon-box-icon"><img class="svg" src="{{ asset('psychologist_icons_svg/loc2.svg') }}" alt="" /></i>
          <div class="mad-icon-box-content">
            <p>Варна, България</p>
          </div>
        </article>
      </div>
      <div class="mad-col">
        <article class="mad-icon-box">
          <i class="mad-icon-box-icon"><img class="svg" src="{{ asset('psychologist_icons_svg/phone2.svg') }}" alt="" /></i>
          <div class="mad-icon-box-content">
            <p>0897 416 375 <br /><a href="mailto:info@mindbloombg.com">info@mindbloombg.com</a></p>
          </div>
        </article>
      </div>
      <div class="mad-col">
        <article class="mad-icon-box">
          <i class="mad-icon-box-icon"><img class="svg" src="{{ asset('psychologist_icons_svg/clock2.svg') }}" alt="" /></i>
          <div class="mad-icon-box-content">
            <p>Понеделник - Петък: 9:00 - 18:00</p>
          </div>
        </article>
      </div>
      <div class="mad-col">
        <article class="mad-icon-box">
          <i class="mad-icon-box-icon"><img class="svg" src="{{ asset('psychologist_icons_svg/network2.svg') }}" alt="" /></i>
          <div class="mad-icon-box-content">
            <p>
              <a href="#" target="_blank">Facebook</a> - <a href="#" target="_blank">Instagram</a> - <a href="#" target="_blank">LinkedIn</a>
            </p>
          </div>
        </article>
      </div>
    </div>
  </div>
</div>

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

<header id="mad-header" class="mad-header header-2 mad-header--transparent mad-header--transparent-single">
  <div class="mad-middle-header mad-header-section--sticky-xl">
    <div class="container-fluid">
      <div class="mad-header-items">
        <div class="mad-header-item">
          <div class="mad-actions">
            <div class="mad-col">
              <button id="mad-nav-btn" class="mad-nav-btn">
                <span class="line line-top"></span><span class="line line-center"></span><span class="line line-bottom"></span>
              </button>
            </div>
            <div class="mad-col">
              <div class="mad-social-icons style-2 size-small">
                <ul>
                  <li><a href="https://www.facebook.com/mybio.net" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>
                </ul>
              </div>
            </div>
            <div class="mad-col">
              <a href="#" data-arctic-modal="#search-modal"><i class="material-icons">search</i></a>
            </div>
          </div>
        </div>
        <div class="mad-header-item">
          <a href="{{ route('home') }}" class="logo"><img src="{{ asset('images/logo-mindbloom.png') }}" alt="MindBloom" /></a>
        </div>
        <div class="mad-header-item">
          <div class="mad-actions">
            <div class="mad-col">
              <div class="mad-our-info">
                <div class="mad-our-info">
                  <div class="mad-info">
                    <i><img src="{{ asset('psychologist_icons_svg/phone3.svg') }}" alt="" class="svg" /></i>
                    <span><b>0897 416 375</b></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="mad-col">
              <a href="{{ route('contact') }}" class="btn">ЗАПАЗИ СВОЕТО МЯСТО</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
