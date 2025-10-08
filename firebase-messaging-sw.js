importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyBk2M_Mh1u_kYMIfcik4GkoowtDdo_tyi8",
    authDomain: "bbbring-bfed8.firebaseapp.com",
    projectId: "bbbring-bfed8",
    storageBucket: "bbbring-bfed8.firebasestorage.app",
    messagingSenderId: "182249567851",
    appId: "1:182249567851:web:814256dba04c4f1a73632e",
    measurementId: "F-12345678"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    return self.registration.showNotification(payload.data.title, {
        body: payload.data.body ? payload.data.body : '',
        icon: payload.data.icon ? payload.data.icon : ''
    });
});