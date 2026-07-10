const CACHE_NAME = 'ada-co-os-cache-v2';
const ASSETS_TO_CACHE = [
  '/',
  '/images/ada-co-os-logo-transparent.svg',
  '/images/ada-co-os-logo-transparent.png',
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event (Network-First with Cache Fallback)
self.addEventListener('fetch', (event) => {
  // Only handle GET requests
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Cache successful requests dynamically
        if (response.status === 200) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Fallback to cache if network fails
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // Return offline custom page or response
          return new Response(
            `<html>
              <head>
                <meta charset="utf-8">
                <title>Çevrimdışı Mod — ADA Co-OS</title>
                <style>
                  body { font-family: system-ui, sans-serif; background: #0c0a09; color: #f5f5f4; text-align: center; padding: 50px 20px; }
                  h1 { font-size: 24px; color: #10b981; }
                  p { color: #a8a29e; font-size: 14px; }
                </style>
              </head>
              <body>
                <h1>🌐 İnternet Bağlantısı Yok</h1>
                <p>ADA Co-OS şu anda çevrimdışı modda çalışıyor. Lütfen bağlantınızı kontrol edin.</p>
              </body>
            </html>`,
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        });
      })
  );
});
