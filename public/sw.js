// Service Worker untuk Push Notification "Lapor Bu!!"
// File ini WAJIB berada di root /public (bukan di dalam folder), supaya
// scope-nya mencakup seluruh halaman website.

self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    let payload = {};
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { title: 'Notifikasi', body: event.data.text() };
    }

    const title = payload.title || 'Lapor Bu!!';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/asset/logo.png',
        badge: payload.badge || '/asset/logo.png',
        data: payload.data || {},
        tag: payload.tag || undefined,
        renotify: !!payload.tag,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
            // Cari tab yang sedang terbuka di origin (domain) yang sama.
            // Tidak harus persis sama URL-nya, cukup satu domain,
            // supaya tidak terus-menerus membuka tab baru.
            let targetOrigin;
            try {
                targetOrigin = new URL(url, self.location.origin).origin;
            } catch (e) {
                targetOrigin = self.location.origin;
            }

            let matchingClient = null;

            for (const client of windowClients) {
                let clientOrigin;
                try {
                    clientOrigin = new URL(client.url).origin;
                } catch (e) {
                    continue;
                }

                if (clientOrigin === targetOrigin) {
                    matchingClient = client;
                    break;
                }
            }

            if (matchingClient) {
                // Tab sudah ada di domain yang sama -> fokuskan lalu arahkan
                // ke URL notifikasi (kalau browser mendukung navigate()).
                if ('navigate' in matchingClient) {
                    return matchingClient.focus().then(() => matchingClient.navigate(url));
                }
                return matchingClient.focus();
            }

            // Tidak ada tab yang cocok -> buka tab/jendela baru.
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});