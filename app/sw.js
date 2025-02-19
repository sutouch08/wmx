// the cache version gets updated every time there is a new deployment
const version = 1;
let cacheName = `cacheName-${version}`;
let dynamicName = `dynamicCache`;


// these are the routes we are going to cache for offline support
let cacheFiles = [
  '/sttc/pwa',
  '/sttc/pwa/index.html',
  '/sttc/pwa/login.html',
  '/sttc/pwa/transfer.html',
  '/sttc/pwa/scripts/app.js',
  '/sttc/pwa/scripts/login.js',
  '/sttc/pwa/scripts/transfer/transfer.js',
  '/sttc/pwa/scripts/transfer/transfer_list.js',
  '/sttc/pwa/assets/js/ace-extra.js',
  '/sttc/pwa/assets/js/jquery.min.js',
  '/sttc/pwa/assets/js/jquery-ui-1.10.4.custom.min.js',
  '/sttc/pwa/assets/js/bootstrap.js',
  '/sttc/pwa/assets/js/ace.js',
  '/sttc/pwa/assets/js/sweet-alert.js',
  '/sttc/pwa/assets/js/handlebars-v3.js',
  '/sttc/pwa/assets/css/bootstrap.css',
  '/sttc/pwa/assets/css/font-awesome.css',
  '/sttc/pwa/assets/css/ace-fonts.css',
  '/sttc/pwa/assets/css/ace.css',
  '/sttc/pwa/assets/css/jquery-ui-1.10.4.custom.min.css',
  '/sttc/pwa/assets/css/template.css',
  '/sttc/pwa/assets/css/sweet-alert.css'
];


self.addEventListener('install', (ev) => {
  console.log(`Version ${version} installed`);

  ev.waitUntil(
    caches.open(cacheName).then(
      cache => {
        cache.addAll(cacheFiles).then(
          () => {
            console.log(`${cacheName} has been updated`);
          },
          (err) => {
            console.warn(`Failed to update ${cacheName}`);
          }
        );
    })
  );
});



self.addEventListener('activated', (ev) => {
  console.log('activated');
  ev.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys
          .filter((key) => key != cacheName)
          .map((key) => cache.delete(key))
      );
    })
  );
});


self.addEventListener('fetch', (ev) => {
  console.log(`Fetch request for : ${ev.request.url}`);

  //version 1 - pass thru
  ev.respondWith(fetch(ev.request));

  // version 2 - check the caches first for the files. If missing do a Fetch
  // ev.respondWith(
  //   caches.match(ev.request).then((cacheRes) => {
  //     if(cacheRes == undefined) {
  //       console.log(`Missing ${ev.request.url}`);
  //     }
  //
  //     return cacheRes || fetch(ev.request);
  //   })
  // );

  // version 3 - check caches. Fetch if missing. then add response to cache
  // ev.respondWith(
  //   caches.match(ev.request).then((cacheRes) => {
  //     return cacheRes || fetch(ev.request.url)
  //     .then( fetchResponse => {
  //       caches.open(cacheName).then(cache => {
  //         cache.put(ev.request, fetchResponse.clone());
  //         return fetchResponse;
  //       })
  //     })
  //   })
  // );
});
