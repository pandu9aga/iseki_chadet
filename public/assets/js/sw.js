const CACHE_NAME = 'ocr-cache-v1';
const urlsToCache = [
  '/iseki_chadet/public/assets/js/ocr.js',
  '/iseki_chadet/public/assets/js/models/ch_PP-OCRv2_det_fuse_activation/model.json',
  '/iseki_chadet/public/assets/js/models/ch_PP-OCRv2_rec_fuse_activation/model.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => response || fetch(event.request))
  );
});
