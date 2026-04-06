(function () {
  'use strict';

  window.toolverseRunTool = function (slug) {
    if (typeof toolverseData === 'undefined') return;
    var inputEl = document.getElementById('tool-input-' + slug);
    var outEl = document.getElementById('tool-output-' + slug);
    var input = inputEl ? inputEl.value : '';
    var url = toolverseData.restUrl + 'tools/' + encodeURIComponent(slug) + '/run';
    fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': toolverseData.nonce,
      },
      body: JSON.stringify({ input: input }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (json) {
        if (!outEl) return;
        var payload = json && json.data !== undefined ? json.data : json;
        outEl.value = typeof payload === 'string' ? payload : JSON.stringify(payload, null, 2);
      })
      .catch(function () {
        if (outEl) outEl.value = 'Request failed.';
      });
  };

  document.querySelectorAll('[data-favorite]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (typeof toolverseData === 'undefined' || !toolverseData.isLogged) {
        window.location.href = toolverseData ? toolverseData.homeUrl + 'login/' : '/login/';
        return;
      }
      var slug = btn.getAttribute('data-favorite');
      fetch(toolverseData.restUrl + 'favorites', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': toolverseData.nonce,
        },
        body: JSON.stringify({ slug: slug, on: true }),
      }).catch(function () {});
    });
  });
})();
