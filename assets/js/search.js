(function () {
  'use strict';

  var input = document.getElementById('homepage-search');
  var dropdown = document.getElementById('search-results-dropdown');
  var btn = document.querySelector('.search-btn-xl');

  if (!input || !dropdown || typeof toolverseData === 'undefined') return;

  var debounce;

  function runSearch() {
    var q = input.value.trim();
    if (q.length < 2) {
      dropdown.hidden = true;
      dropdown.innerHTML = '';
      return;
    }
    var url = toolverseData.restUrl + 'tools?search=' + encodeURIComponent(q);
    fetch(url, { credentials: 'same-origin' })
      .then(function (r) {
        return r.json();
      })
      .then(function (items) {
        dropdown.innerHTML = '';
        if (!items || !items.length) {
          dropdown.hidden = true;
          return;
        }
        items.slice(0, 8).forEach(function (t) {
          var a = document.createElement('a');
          a.href = t.url;
          a.textContent = t.name;
          dropdown.appendChild(a);
        });
        dropdown.hidden = false;
      })
      .catch(function () {
        dropdown.hidden = true;
      });
  }

  input.addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(runSearch, 200);
  });

  if (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      runSearch();
    });
  }
})();
