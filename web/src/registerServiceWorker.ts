/* eslint-disable no-console */

import { register } from 'register-service-worker';

if (process.env.NODE_ENV === 'production') {
  register(`${process.env.BASE_URL}service-worker.js`, {
    ready() {
      console.log(
        'App is being served from cache by a service worker.\n'
        + 'For more details, visit https://goo.gl/AFskqB',
      );
    },
    registered(registration) {
      console.log('Service worker has been registered.');
      const checkForUpdates = () => {
        console.log('Checking for service worker updates.');
        registration.update();
      };
      // Check for updates frequently (currently: every 5 minutes)
      setInterval(checkForUpdates, 1000 * 60 * 5);
    },
    cached() {
      console.log('Content has been cached for offline use.');
    },
    updatefound() {
      console.log('New content is downloading.');
    },
    updated(registration) {
      console.log('New content is available; please refresh.');
      const event = new CustomEvent('worker:updated', {
        detail: registration,
      });
      document.dispatchEvent(event);
    },
    offline() {
      console.log('No internet connection found. App is running in offline mode.');
    },
    error(error) {
      console.error('Error during service worker registration:', error);
    },
  });
}
