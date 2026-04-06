(function () {
  'use strict';

  if (typeof toolverseData === 'undefined') return;

  var toggle = document.getElementById('sidebar-toggle');
  var sidebar = document.getElementById('dashboard-sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      var open = sidebar.classList.toggle('collapsed');
      toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
    });
  }

  document.querySelectorAll('.sidebar-nav .nav-item').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var panel = link.getAttribute('data-panel');
      if (!panel || !panel.length) return;
      e.preventDefault();
      document.querySelectorAll('.sidebar-nav .nav-item').forEach(function (a) {
        a.classList.toggle('active', a === link);
      });
      document.querySelectorAll('.dash-panel').forEach(function (p) {
        p.classList.remove('active');
        p.hidden = true;
      });
      var el = document.getElementById('panel-' + panel);
      if (el) {
        el.hidden = false;
        el.classList.add('active');
      }
    });
  });

  function rest(path, opts) {
    return fetch(toolverseData.restUrl + path, {
      credentials: 'same-origin',
      headers: Object.assign(
        { 'X-WP-Nonce': toolverseData.nonce },
        (opts && opts.headers) || {}
      ),
      ...opts,
    }).then(function (r) {
      return r.json();
    });
  }

  rest('usage')
    .then(function (json) {
      var total = document.getElementById('total-tools-used');
      if (total && json.total !== undefined) total.textContent = json.total;
      var act = document.getElementById('recent-activity');
      var hist = document.getElementById('history-list');
      if (json.recent && act) {
        act.innerHTML = json.recent
          .map(function (row) {
            return '<div class="activity-row">' + row.tool_slug + ' · ' + row.used_at + '</div>';
          })
          .join('');
      }
      if (json.recent && hist) {
        hist.innerHTML = act ? act.innerHTML : '';
      }
    })
    .catch(function () {});

  rest('favorites')
    .then(function (slugs) {
      var n = Array.isArray(slugs) ? slugs.length : 0;
      var tf = document.getElementById('total-favorites');
      if (tf) tf.textContent = n;
      var list = document.getElementById('favorites-list');
      if (list && Array.isArray(slugs)) {
        list.innerHTML = slugs
          .map(function (s) {
            return '<li><a href="' + toolverseData.homeUrl + 'tool/' + encodeURIComponent(s) + '/">' + s + '</a></li>';
          })
          .join('');
      }
      var quick = document.getElementById('quick-tools');
      if (quick && slugs && slugs.length) {
        quick.innerHTML = slugs
          .slice(0, 6)
          .map(function (s) {
            return (
              '<a class="tool-card" href="' +
              toolverseData.homeUrl +
              'tool/' +
              encodeURIComponent(s) +
              '/"><span class="tool-card-title">' +
              s +
              '</span></a>'
            );
          })
          .join('');
      }
    })
    .catch(function () {});

  var saveProfile = document.getElementById('save-profile');
  if (saveProfile) {
    saveProfile.addEventListener('click', function () {
      var body = new URLSearchParams();
      body.set('action', 'toolverse_save_profile');
      body.set('nonce', toolverseData.authNonce);
      body.set('display_name', document.getElementById('set-display-name').value);
      body.set('email', document.getElementById('set-email').value);
      fetch(toolverseData.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
        .then(function (r) {
          return r.json();
        })
        .then(function (j) {
          alert(j.success ? j.data.message : j.data.message);
        });
    });
  }

  var chg = document.getElementById('change-password');
  if (chg) {
    chg.addEventListener('click', function () {
      var body = new URLSearchParams();
      body.set('action', 'toolverse_change_password');
      body.set('nonce', toolverseData.authNonce);
      body.set('current', document.getElementById('set-current-pass').value);
      body.set('new', document.getElementById('set-new-pass').value);
      body.set('confirm', document.getElementById('set-confirm-pass').value);
      fetch(toolverseData.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
        .then(function (r) {
          return r.json();
        })
        .then(function (j) {
          alert(j.success ? j.data.message : j.data.message);
        });
    });
  }
})();
