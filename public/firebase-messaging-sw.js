importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyAy3CM2I2OREwSWHPkWVQ0_sVk-3e415WM",
  authDomain: "sentimen-notif.firebaseapp.com",
  projectId: "sentimen-notif",
  storageBucket: "sentimen-notif.firebasestorage.app",
  messagingSenderId: "441496587129",
  appId: "1:441496587129:web:3cd9cd4ac13acf87083da8"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  
  const notificationTitle = payload.notification?.title || 'Notifikasi SENTIMEN';
  const notificationOptions = {
    body: payload.notification?.body || 'Anda memiliki pemberitahuan baru.',
    icon: '/images/logo.png',
    data: payload.data
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
