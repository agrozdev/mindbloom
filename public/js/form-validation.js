(function () {
  var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function clearError(field) {
    var item = field.closest('.mad-form-item');

    if (!item) {
      return;
    }

    item.classList.remove('has-error');

    var existing = item.querySelector('.mad-error-message');

    if (existing) {
      existing.remove();
    }
  }

  function showError(field, message) {
    var item = field.closest('.mad-form-item');

    if (!item) {
      return;
    }

    item.classList.add('has-error');

    var span = document.createElement('span');
    span.className = 'mad-error-message';
    span.textContent = message;
    item.appendChild(span);
  }

  function isFieldValid(field) {
    if (field.type === 'checkbox') {
      return field.checked;
    }

    if (!field.value.trim()) {
      return false;
    }

    if (field.type === 'email') {
      return EMAIL_PATTERN.test(field.value.trim());
    }

    return true;
  }

  function attach(form) {
    form.addEventListener('submit', function (event) {
      var fields = form.querySelectorAll('[required]');
      var firstInvalid = null;

      fields.forEach(function (field) {
        clearError(field);

        if (!isFieldValid(field)) {
          var message = field.dataset.error || 'Това поле е задължително.';
          showError(field, message);

          if (!firstInvalid) {
            firstInvalid = field;
          }
        }
      });

      if (firstInvalid) {
        event.preventDefault();
        firstInvalid.focus();
        firstInvalid.closest('.mad-form-item')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    // Clear a field's error as soon as the visitor starts fixing it.
    form.querySelectorAll('[required]').forEach(function (field) {
      var eventName = field.type === 'checkbox' ? 'change' : 'input';
      field.addEventListener(eventName, function () {
        if (isFieldValid(field)) {
          clearError(field);
        }
      });
    });
  }

  document.querySelectorAll('form[data-validate]').forEach(attach);
})();
