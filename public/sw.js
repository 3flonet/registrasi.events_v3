const CACHE_NAME = 'reg-events-cache-v2';
const ASSETS_TO_CACHE = [
    '/offline.html',
];

// Install Event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(ASSETS_TO_CACHE);
            })
    );
    self.skipWaiting(); // Force the waiting service worker to become the active service worker
});

// Activate Event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Fetch Event
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    if (!['http:', 'https:'].includes(url.protocol)) return;

    // STRATEGY: Network First for HTML/Navigation & Admin Routes
    // This prevents "This page has expired" by ensuring fresh CSRF tokens
    if (event.request.mode === 'navigate' || url.pathname.startsWith('/admin')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match('/offline.html');
                })
        );
        return;
    }

    // STRATEGY: Stale-While-Revalidate for Static Assets (Images, JS, CSS)
    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                const fetchPromise = fetch(event.request).then(networkResponse => {
                    // Cache the new response if it's valid
                    if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // If network fails and no cache, return nothing (or fallback image if needed)
                    return cachedResponse;
                });

                return cachedResponse || fetchPromise;
            })
    );
});
