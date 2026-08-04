(function () {
  var STORAGE_KEY = 'mb_cookie_consent';
  var banner = document.getElementById('mad-cookie-consent');

  if (!banner) {
    return;
  }

  function applyStoredConsent() {
    var stored = window.localStorage.getItem(STORAGE_KEY);

    if (stored === 'accepted' && typeof window.gtag === 'function') {
      window.gtag('consent', 'update', {
        analytics_storage: 'granted',
        ad_storage: 'granted',
      });
    }

    return stored;
  }

  function setConsent(value) {
    window.localStorage.setItem(STORAGE_KEY, value);
    banner.classList.remove('is-visible');

    if (value === 'accepted' && typeof window.gtag === 'function') {
      window.gtag('consent', 'update', {
        analytics_storage: 'granted',
        ad_storage: 'granted',
      });
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
