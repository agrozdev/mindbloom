(function () {
  var STORAGE_KEY = 'mb_cookie_consent';
  var banner = document.getElementById('mad-cookie-consent');

  if (!banner) {
    return;
  }

  function grantConsent() {
    if (typeof window.loadGoogleAnalytics === 'function') {
      window.loadGoogleAnalytics();
    }

    if (typeof window.loadMetaPixel === 'function') {
      window.loadMetaPixel();
    }
  }

  // Used when a visitor rejects, including after having accepted earlier via
  // the "change cookie settings" button: stop GA and remove its cookies.
  function revokeConsent() {
    if (window.gaMeasurementId) {
      window['ga-disable-' + window.gaMeasurementId] = true;
    }

    if (window.gaLoaded && typeof window.gtag === 'function') {
      window.gtag('consent', 'update', { analytics_storage: 'denied' });
    }

    var domain = window.location.hostname.replace(/^www\./, '');

    document.cookie.split(';').forEach(function (cookie) {
      var name = cookie.split('=')[0].trim();

      if (/^(_ga|_gid|_gat)/.test(name)) {
        [domain, '.' + domain, ''].forEach(function (d) {
          document.cookie = name + '=; Max-Age=0; path=/' + (d ? '; domain=' + d : '');
        });
      }
    });
  }

  function applyStoredConsent() {
    var stored = null;

    try {
      stored = window.localStorage.getItem(STORAGE_KEY);
    } catch (e) {}

    if (stored === 'accepted') {
      grantConsent();
    }

    return stored;
  }

  function setConsent(value) {
    try {
      window.localStorage.setItem(STORAGE_KEY, value);
    } catch (e) {}

    banner.classList.remove('is-visible');

    if (value === 'accepted') {
      grantConsent();
    } else {
      revokeConsent();
    }
  }

  var stored = applyStoredConsent();

  if (stored !== 'accepted' && stored !== 'rejected') {
    banner.classList.add('is-visible');
  }

  var acceptBtn = document.getElementById('mad-cookie-accept');
  var rejectBtn = document.getElementById('mad-cookie-reject');

  if (acceptBtn) {
    acceptBtn.addEventListener('click', function () {
      setConsent('accepted');
    });
  }

  if (rejectBtn) {
    rejectBtn.addEventListener('click', function () {
      setConsent('rejected');
    });
  }

  // Exposed so the "change cookie settings" link/button (e.g. on the cookie
  // policy page) can bring the banner back up at any time.
  window.madReopenCookieConsent = function () {
    banner.classList.add('is-visible');
  };
})();
