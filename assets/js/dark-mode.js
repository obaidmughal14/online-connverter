(function () {
  'use strict';
  var STORAGE_KEY = 'toolverse-theme';
  var html = document.documentElement;
  var saved = localStorage.getItem(STORAGE_KEY);
  var preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  var theme = saved || preferred;
  html.setAttribute('data-theme', theme);

  window.__setTheme = function (newTheme) {
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem(STORAGE_KEY, newTheme);
    document.querySelectorAll('.theme-toggle-btn').forEach(function (btn) {
      btn.setAttribute('aria-label', newTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
      var icon = btn.querySelector('.toggle-icon');
      if (icon) icon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
    });
  };

  window.__toggleTheme = function () {
    var current = html.getAttribute('data-theme');
    window.__setTheme(current === 'dark' ? 'light' : 'dark');
  };

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
    if (!localStorage.getItem(STORAGE_KEY)) {
      window.__setTheme(e.matches ? 'dark' : 'light');
    }
  });
})();
