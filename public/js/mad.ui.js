(function () {
  'use strict';

  var toast;
  var hideTimeout;
  var COPY_MESSAGE = 'Копирането на съдържание от сайта не е разрешено.';
  var INSPECT_MESSAGE = 'Тази функция е забранена на този сайт.';

  function ensureToast() {
    if (toast) {
      return toast;
    }
    toast = document.createElement('div');
    toast.className = 'mad-copy-warning';
    document.body.appendChild(toast);
    return toast;
  }

  function showWarning(message) {
    var el = ensureToast();
    el.textContent = message;
    el.classList.add('is-visible');
    clearTimeout(hideTimeout);
    hideTimeout = setTimeout(function () {
      el.classList.remove('is-visible');
    }, 2500);
  }

  function isFormField(target) {
    if (!target) {
      return false;
    }
    var tag = target.tagName;
    return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || target.isContentEditable;
  }

  document.addEventListener('selectstart', function (e) {
    if (isFormField(e.target)) {
      return;
    }
    e.preventDefault();
    showWarning(COPY_MESSAGE);
  });

  document.addEventListener('copy', function (e) {
    if (isFormField(e.target)) {
      return;
    }
    e.preventDefault();
    showWarning(COPY_MESSAGE);
  });

  document.addEventListener('contextmenu', function (e) {
    if (isFormField(e.target)) {
      return;
    }
    e.preventDefault();
    showWarning(INSPECT_MESSAGE);
  });

  document.addEventListener('keydown', function (e) {
    var key = (e.key || '').toUpperCase();

    if (key === 'F12') {
      e.preventDefault();
      showWarning(INSPECT_MESSAGE);
      return;
    }

    if (e.ctrlKey && e.shiftKey && (key === 'I' || key === 'J' || key === 'C')) {
      e.preventDefault();
      showWarning(INSPECT_MESSAGE);
      return;
    }

    if (e.metaKey && e.altKey && (key === 'I' || key === 'J' || key === 'C')) {
      // Safari on macOS
      e.preventDefault();
      showWarning(INSPECT_MESSAGE);
    }
  });
})();
