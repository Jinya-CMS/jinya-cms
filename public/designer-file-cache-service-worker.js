function cacheFiles(resolve, reject) {
  const openRequest = self.indexedDB.open('files');
  openRequest.onerror = () => reject(openRequest.error);
  openRequest.onsuccess = () => {
    const db = openRequest.result;
    const os = db.transaction('files')
      .objectStore('files');
    const request = os.getAll();
    request.onerror = () => reject(request.error);
    request.onsuccess = async () => {
      if (request.result) {
        const files = request.result;
        const filesCache = await caches.open('jinya-files-cache');
        const keys = await filesCache.keys();
        const cachedUrls = keys.map((key) => key.url);
        const urlsToCache = new Set(files
          .filter((file) => !cachedUrls.includes(file.path))
          .map((file) => file.path));

        await filesCache.addAll([...urlsToCache]);
      }

      resolve();
    };
  };
}

async function proxyFetch(request) {
  const filesCache = await caches.open('jinya-files-cache');
  const cachedResponse = await filesCache.match(request);
  if (cachedResponse) {
    return cachedResponse;
  }

  const networkResponse = await fetch(request);
  if (networkResponse.type !== 'opaque' && networkResponse.ok === false) {
    throw new Error('Resource not available');
  }

  filesCache.put(request, networkResponse.clone());

  return networkResponse;
}

self.addEventListener('install', (event) => {
  event.waitUntil(new Promise(cacheFiles));
});

self.addEventListener('activate', (event) => {
  event.waitUntil(new Promise(cacheFiles));
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET' || !request.url.match(/\/jinya-content\/[a-z0-9]{64}$/gmi)) {
    return;
  }

  event.respondWith(proxyFetch(request));
});
