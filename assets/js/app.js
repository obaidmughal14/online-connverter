(function () {
  'use strict';

  var hamburger = document.getElementById('hamburger');
  var mobileNav = document.getElementById('mobile-nav');

  if (hamburger && mobileNav) {
    hamburger.addEventListener('click', function () {
      var open = mobileNav.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
      mobileNav.setAttribute('aria-hidden', open ? 'false' : 'true');
      document.body.classList.toggle('nav-open', open);
    });
  }

  document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.faq-item');
      if (!item) return;
      var ans = item.querySelector('.faq-answer');
      if (!ans) return;
      var expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      ans.hidden = expanded;
    });
  });

  document.querySelectorAll('[data-tool-run]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var slug = btn.getAttribute('data-tool-run');
      if (window.toolverseRunTool) window.toolverseRunTool(slug);
    });
  });

  document.querySelectorAll('[data-share-url]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = btn.getAttribute('data-share-url');
      if (navigator.share) {
        navigator.share({ title: document.title, url: url }).catch(function () {});
      } else if (navigator.clipboard && url) {
        navigator.clipboard.writeText(url).then(function () {
          btn.textContent = '✓ Copied';
        });
      }
    });
  });

  var statNums = document.querySelectorAll('.stat-number[data-count]');
  if (statNums.length && 'IntersectionObserver' in window) {
    var obs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10);
          var start = 0;
          var dur = 900;
          var t0 = performance.now();
          function tick(now) {
            var p = Math.min(1, (now - t0) / dur);
            el.textContent = Math.floor(start + (target - start) * p).toLocaleString();
            if (p < 1) requestAnimationFrame(tick);
          }
          requestAnimationFrame(tick);
          obs.unobserve(el);
        });
      },
      { threshold: 0.2 }
    );
    statNums.forEach(function (el) {
      obs.observe(el);
    });
  }

  var toolsFilter = document.getElementById('tools-filter-input');
  var toolsGrid = document.getElementById('tools-archive-grid');
  if (toolsFilter && toolsGrid) {
    toolsFilter.addEventListener('input', function () {
      var q = toolsFilter.value.toLowerCase();
      toolsGrid.querySelectorAll('.tool-card').forEach(function (card) {
        var name = (card.getAttribute('data-name') || '') + ' ' + (card.getAttribute('data-slug') || '');
        card.style.display = !q || name.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  }
})();
