(function () {
  'use strict';

  var tabs = document.querySelectorAll('.auth-tab');
  var panels = { login: document.getElementById('tab-login'), register: document.getElementById('tab-register') };

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var name = tab.getAttribute('data-tab');
      tabs.forEach(function (t) {
        t.classList.toggle('active', t === tab);
      });
      Object.keys(panels).forEach(function (key) {
        if (panels[key]) panels[key].classList.toggle('active', key === name);
      });
    });
  });

  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap = btn.closest('.input-wrapper');
      if (!wrap) return;
      var inp = wrap.querySelector('input');
      if (!inp) return;
      inp.type = inp.type === 'password' ? 'text' : 'password';
    });
  });

  function showMsg(id, msg, ok) {
    var el = document.getElementById(id);
    if (!el) return;
    el.hidden = false;
    el.textContent = msg;
    el.className = 'auth-message' + (ok ? ' success' : '');
  }

  var loginBtn = document.getElementById('login-btn');
  if (loginBtn && typeof toolverseData !== 'undefined') {
    loginBtn.addEventListener('click', function () {
      var u = document.getElementById('login-username');
      var p = document.getElementById('login-password');
      var r = document.getElementById('login-remember');
      loginBtn.querySelector('.btn-loader') && loginBtn.querySelector('.btn-loader').classList.remove('hidden');
      var body = new URLSearchParams();
      body.set('action', 'toolverse_login');
      body.set('nonce', toolverseData.authNonce);
      body.set('username', u ? u.value : '');
      body.set('password', p ? p.value : '');
      if (r && r.checked) body.set('remember', '1');
      fetch(toolverseData.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
        .then(function (x) {
          return x.json();
        })
        .then(function (json) {
          loginBtn.querySelector('.btn-loader') && loginBtn.querySelector('.btn-loader').classList.add('hidden');
          if (json.success) {
            showMsg('login-message', json.data.message, true);
            window.location.href = json.data.redirect || toolverseData.homeUrl + 'dashboard/';
          } else {
            showMsg('login-message', (json.data && json.data.message) || 'Error', false);
          }
        })
        .catch(function () {
          loginBtn.querySelector('.btn-loader') && loginBtn.querySelector('.btn-loader').classList.add('hidden');
          showMsg('login-message', 'Network error', false);
        });
    });
  }

  var regBtn = document.getElementById('register-btn');
  if (regBtn && typeof toolverseData !== 'undefined') {
    regBtn.addEventListener('click', function () {
      var name = document.getElementById('reg-name');
      var user = document.getElementById('reg-username');
      var email = document.getElementById('reg-email');
      var pass = document.getElementById('reg-password');
      var terms = document.getElementById('reg-terms');
      if (terms && !terms.checked) {
        showMsg('register-message', 'Please accept the terms.', false);
        return;
      }
      regBtn.querySelector('.btn-loader') && regBtn.querySelector('.btn-loader').classList.remove('hidden');
      var body = new URLSearchParams();
      body.set('action', 'toolverse_register');
      body.set('nonce', toolverseData.authNonce);
      body.set('display_name', name ? name.value : '');
      body.set('username', user ? user.value : '');
      body.set('email', email ? email.value : '');
      body.set('password', pass ? pass.value : '');
      fetch(toolverseData.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
        .then(function (x) {
          return x.json();
        })
        .then(function (json) {
          regBtn.querySelector('.btn-loader') && regBtn.querySelector('.btn-loader').classList.add('hidden');
          if (json.success) {
            showMsg('register-message', json.data.message, true);
            window.location.href = json.data.redirect || toolverseData.homeUrl + 'dashboard/';
          } else {
            showMsg('register-message', (json.data && json.data.message) || 'Error', false);
          }
        })
        .catch(function () {
          regBtn.querySelector('.btn-loader') && regBtn.querySelector('.btn-loader').classList.add('hidden');
          showMsg('register-message', 'Network error', false);
        });
    });
  }
})();
