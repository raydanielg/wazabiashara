/* Wazabiashara Service Worker — PWA Offline Support */
const CACHE_VERSION = 'wazabiashara-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;
const IMAGE_CACHE = `${CACHE_VERSION}-images`;

/* Assets to cache on install */
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/logo.png',
  '/favicon.png',
];

/* Cache strategies */
const CACHE_STRATEGIES = {
  /* Stale-while-revalidate for CSS/JS */
  staleWhileRevalidate: (cache, request) =>
    cache.match(request).then(cached => {
      const fetchPromise = fetch(request).then(response => {
        if (response && response.status === 200) {
          cache.put(request, response.clone());
        }
        return response;
      }).catch(() => cached);
      return cached || fetchPromise;
    }),

  /* Cache-first for images */
  cacheFirst: (cache, request) =>
    cache.match(request).then(cached =>
      cached || fetch(request).then(response => {
        if (response && response.status === 200) {
          cache.put(request, response.clone());
        }
        return response;
      }).catch(() => cached)
    ),

  /* Network-first for pages */
  networkFirst: (cache, request) =>
    fetch(request).then(response => {
      if (response && response.status === 200) {
        cache.put(request, response.clone());
      }
      return response;
    }).catch(() => cache.match(request).then(cached =>
      cached || caches.match('/offline.html')
    ))
};

/* Install — cache static assets */
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

/* Activate — clean old caches */
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(key => !key.startsWith(CACHE_VERSION))
          .map(key => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

/* Fetch — route requests to appropriate strategy */
self.addEventListener('fetch', event => {
  const { request } = event;

  /* Skip non-GET requests */
  if (request.method !== 'GET') return;

  /* Skip cross-origin requests */
  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  /* Skip API/AJAX requests */
  if (url.pathname.startsWith('/api/') || request.headers.get('accept')?.includes('application/json')) return;

  /* Route by type */
  if (request.destination === 'image' || url.pathname.match(/\.(png|jpg|jpeg|gif|svg|webp|ico)$/i)) {
    event.respondWith(
      caches.open(IMAGE_CACHE).then(cache => CACHE_STRATEGIES.cacheFirst(cache, request))
    );
  } else if (request.destination === 'style' || request.destination === 'script' || request.destination === 'font') {
    event.respondWith(
      caches.open(RUNTIME_CACHE).then(cache => CACHE_STRATEGIES.staleWhileRevalidate(cache, request))
    );
  } else if (request.mode === 'navigate') {
    event.respondWith(
      caches.open(RUNTIME_CACHE).then(cache => CACHE_STRATEGIES.networkFirst(cache, request))
    );
  }
});

/* Message — skip waiting for updates */
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
