const CACHE_NAME = 'bankos-portal-v1';
const STATIC_ASSETS = [
    '/',
    '/dashboard',
    '/offline.html',
];

// ── Install: cache static shell ──
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

// ── Activate: clean old caches ──
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// ── Fetch: network-first, offline fallback ──
self.addEventListener('fetch', event => {
    const { request } = event;
    // Only intercept same-origin GET requests
    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) return;
    // Skip API/webhook/admin paths
    if (request.url.includes('/api/') || request.url.includes('/logout')) return;

    event.respondWith(
        fetch(request)
            .then(response => {
                // Cache successful HTML pages
                if (response.ok && response.headers.get('content-type')?.includes('text/html')) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(c => c.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request).then(cached => cached || caches.match('/offline.html')))
    );
});

// ── Push notifications ──
self.addEventListener('push', event => {
    if (!event.data) return;
    let data;
    try { data = event.data.json(); } catch { data = { title: 'bankOS', body: event.data.text() }; }

    event.waitUntil(
        self.registration.showNotification(data.title || 'bankOS Portal', {
            body:    data.body || '',
            icon:    data.icon || '/icons/icon-192.png',
            badge:   '/icons/icon-192.png',
            tag:     data.tag  || 'bankos-notification',
            data:    { url: data.url || '/notifications' },
            actions: data.actions || [],
            vibrate: [100, 50, 100],
        })
    );
});

// ── Notification click ──
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const url = event.notification.data?.url || '/notifications';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (const client of list) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }
            clients.openWindow(url);
        })
    );
});
