/*
 * Story gate (AJAX sign-up + reveal) and reading tracking for identified readers.
 * Tracking sends only the story id, active seconds and max scroll %.
 */
(function () {
  'use strict';

  var content = document.querySelector('.story-content[data-story-id]');
  if (!content) return;

  var storyId = content.getAttribute('data-story-id');

  /* ---------- Reading tracking ---------- */

  var tracking = null;

  function startTracking(url, csrf) {
    if (tracking || !url || !csrf) return;

    var seconds = 0;
    var maxScroll = 0;
    var lastSent = '';

    function measureScroll() {
      var rect = content.getBoundingClientRect();
      if (rect.height <= 0) return;
      var seen = (window.innerHeight - rect.top) / rect.height * 100;
      var percent = Math.max(0, Math.min(100, Math.round(seen)));
      if (percent > maxScroll) maxScroll = percent;
    }

    function send() {
      var key = seconds + ':' + maxScroll;
      if (key === lastSent) return;
      lastSent = key;

      var data = new FormData();
      data.append('_token', csrf);
      data.append('post_id', storyId);
      data.append('seconds', String(seconds));
      data.append('scroll', String(maxScroll));

      if (navigator.sendBeacon && navigator.sendBeacon(url, data)) return;

      try {
        fetch(url, { method: 'POST', body: data, credentials: 'same-origin', keepalive: true });
      } catch (e) { /* tracking must never break the page */ }
    }

    // Count only seconds while the tab is actually visible.
    setInterval(function () {
      if (document.visibilityState === 'visible') seconds++;
    }, 1000);

    setInterval(send, 30000);

    window.addEventListener('scroll', measureScroll, { passive: true });
    window.addEventListener('resize', measureScroll, { passive: true });
    document.addEventListener('visibilitychange', function () {
      if (document.visibilityState === 'hidden') send();
    });
    window.addEventListener('pagehide', send);

    measureScroll();
    tracking = { send: send };
  }

  startTracking(content.getAttribute('data-track-url'), content.getAttribute('data-csrf'));

  /* ---------- Gate ---------- */

  var form = document.querySelector('form[data-story-gate]');
  if (!form) return;

  var gate = document.getElementById('story-gate');
  var locked = content.querySelector('.story-locked');
  var button = form.querySelector('button[type=submit]');
  var generalError = form.querySelector('.story-gate-general-error');

  function setError(field, message) {
    var item = form.querySelector('[data-field="' + field + '"]');
    if (!item) {
      generalError.textContent = message;
      generalError.hidden = false;
      return;
    }
    var holder = item.querySelector('.mad-error-message');
    holder.textContent = message;
    holder.hidden = false;
    item.classList.add('has-error');
    var input = item.querySelector('input');
    if (input) input.setAttribute('aria-invalid', 'true');
  }

  function clearErrors() {
    form.querySelectorAll('.mad-error-message').forEach(function (el) {
      el.textContent = '';
      el.hidden = true;
    });
    form.querySelectorAll('.has-error').forEach(function (el) { el.classList.remove('has-error'); });
    form.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
  }

  function validate() {
    var ok = true;
    var name = form.elements.name.value.trim();
    var email = form.elements.email.value.trim();

    if (!name) { setError('name', 'Моля, въведете името си.'); ok = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('email', 'Моля, въведете валиден имейл.'); ok = false; }
    if (!form.elements.consent.checked) { setError('consent', 'Моля, потвърдете съгласието си.'); ok = false; }

    return ok;
  }

  function reveal() {
    content.classList.remove('is-gated');
    if (locked) {
      locked.removeAttribute('inert');
      // Animate from the collapsed preview height to the full height, then release it.
      var from = locked.offsetHeight;
      locked.style.maxHeight = from + 'px';
      locked.classList.add('is-revealing');
      requestAnimationFrame(function () {
        locked.style.maxHeight = locked.scrollHeight + 'px';
      });
      locked.addEventListener('transitionend', function done() {
        locked.removeEventListener('transitionend', done);
        locked.classList.remove('is-revealing');
        locked.style.maxHeight = '';
      });
    }

    gate.classList.add('is-leaving');
    setTimeout(function () {
      gate.remove();
      if (locked) {
        locked.setAttribute('tabindex', '-1');
        locked.focus({ preventScroll: true });
      }
    }, 400);
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    clearErrors();

    if (!validate()) {
      var first = form.querySelector('[aria-invalid="true"]');
      if (first) first.focus();
      return;
    }

    button.disabled = true;

    waitForTurnstile().then(submit);
  });

  // Invisible Turnstile fills its token in the background; a quick submit
  // must wait for it or the server rejects the request.
  function waitForTurnstile() {
    var field = form.querySelector('[name="cf-turnstile-response"]');
    if (!form.querySelector('.cf-turnstile')) return Promise.resolve();

    return new Promise(function (resolve) {
      var waited = 0;
      (function check() {
        field = field || form.querySelector('[name="cf-turnstile-response"]');
        if ((field && field.value) || waited >= 10000) return resolve();
        waited += 250;
        setTimeout(check, 250);
      })();
    });
  }

  function submit() {
    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then(function (response) {
        return response.json().catch(function () { return {}; }).then(function (body) {
          return { status: response.status, body: body };
        });
      })
      .then(function (result) {
        if (result.status >= 200 && result.status < 300) {
          reveal();
          startTracking(
            content.getAttribute('data-track-url') || form.getAttribute('data-track-url'),
            form.elements._token.value
          );
          return;
        }

        button.disabled = false;

        if (result.status === 422 && result.body.errors) {
          Object.keys(result.body.errors).forEach(function (field) {
            setError(field === 'post_id' ? '_general' : field, result.body.errors[field][0]);
          });
          var first = form.querySelector('[aria-invalid="true"]');
          if (first) first.focus();
        } else if (result.status === 429) {
          setError('_general', 'Твърде много опити. Моля, опитайте отново по-късно.');
        } else if (result.status === 419) {
          setError('_general', 'Страницата е отворена твърде дълго. Моля, презаредете я и опитайте отново.');
        } else {
          setError('_general', result.body.message || 'Възникна грешка. Моля, опитайте отново.');
        }

        if (window.turnstile) window.turnstile.reset();
      })
      .catch(function () {
        button.disabled = false;
        setError('_general', 'Няма връзка. Моля, проверете интернета си и опитайте отново.');
      });
  }
})();
