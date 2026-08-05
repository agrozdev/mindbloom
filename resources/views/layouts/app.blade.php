<!DOCTYPE html>
<html lang="bg">

<head>
  <title>@yield('title', 'MindBloom') | Пространството, в което промяната намира своя път</title>
  <meta charset="UTF-8" />
  <meta name="description" content="@yield('meta_description', 'MindBloom — пространството, в което промяната намира своя път, във Варна с индивидуална терапия, групова терапия и уъркшопи.')" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @if (config('app.noindex'))
    <meta name="robots" content="noindex, nofollow" />
  @endif
  @if (config('services.google.site_verification'))
    <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}" />
  @endif
  <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
  @if (config('services.google.analytics_id'))
    {{-- Google Consent Mode: analytics stays denied until the visitor accepts the cookie banner. --}}
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() { dataLayer.push(arguments); }
      gtag('consent', 'default', {
        analytics_storage: 'denied',
        ad_storage: 'denied',
      });
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>
      gtag('js', new Date());
      gtag('config', '{{ config('services.google.analytics_id') }}');
    </script>
  @endif
  <link
    href="https://fonts.googleapis.com/css2?family=Marck+Script&family=Lato:wght@300;400;700&display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css?family=Material+Icons%7CMaterial+Icons+Outlined%7CMaterial+Icons+Two+Tone%7CMaterial+Icons+Round%7CMaterial+Icons+Sharp"
    rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('vendors/owl-carousel/assets/owl.carousel.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('vendors/arcticmodal/jquery.arcticmodal-0.3.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}" />
  {{-- Red theme override, colors from the MindBloom logo. Remove this line to revert to the original blue theme. --}}
  <link rel="stylesheet" href="{{ asset('css/theme-red.css') }}" />
  @stack('styles')
</head>

<body class="mad-body--scheme-brown">
  <div class="mad-preloader"></div>
  <div id="mad-page-wrapper" class="mad-page-wrapper">
    @hasSection('header')
      @yield('header')
    @else
      @include('partials.header')
    @endif

    <main>
      @yield('content')
    </main>

    @include('partials.footer')
  </div>

  @include('partials.cookie-consent')
  @include('partials.phone-fab')

  <script src="{{ asset('vendors/modernizr.js') }}"></script>
  <script src="{{ asset('vendors/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('vendors/jquery.easing.1.3.min.js') }}"></script>
  <script src="{{ asset('vendors/monkeysan.jquery.nav.1.0.js') }}"></script>
  <script src="{{ asset('vendors/owl-carousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('vendors/arcticmodal/jquery.arcticmodal-0.3.min.js') }}"></script>
  <script src="{{ asset('vendors/monkeysan.validator.min.js') }}"></script>
  <script src="{{ asset('vendors/jquery.parallax-1.1.3.min.js') }}"></script>
  <script src="{{ asset('vendors/mad.customselect.js') }}"></script>
  <script src="{{ asset('vendors/handlebars-v4.0.5.min.js') }}"></script>
  <script src="{{ asset('vendors/retina.min.js') }}"></script>
  <script src="{{ asset('js/modules/mad.alert-box.min.js') }}"></script>
  <script src="{{ asset('js/modules/mad.sticky-header-section.min.js') }}"></script>
  <script src="{{ asset('js/mad.app.js') }}"></script>
  <script src="{{ asset('js/cookie-consent.js') }}"></script>
  <script src="{{ asset('js/form-validation.js') }}"></script>
  @stack('scripts')
</body>

</html>
