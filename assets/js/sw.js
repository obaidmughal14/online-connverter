/* global self, caches */
var CACHE_NAME = 'toolverse-v1';
var STATIC_ASSETS = ['/', '/offline.html'];

self.addEventListener('install', function (e) {
  e.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(STATIC_ASSETS).catch(function () {});
    })
  );
});

self.addEventListener('fetch', function (e) {
  if (e.request.method !== 'GET') return;
  e.respondWith(
    caches.match(e.request).then(function (cached) {
      if (cached) return cached;
      return fetch(e.request)
        .then(function (response) {
          return response;
        })
        .catch(function () {
          return caches.match('/offline.html');
        });
    })
  );
});
