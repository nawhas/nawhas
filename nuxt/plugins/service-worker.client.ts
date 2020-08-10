/* eslint-disable no-console */
import { Plugin } from '@nuxt/types';

function updated(registration: ServiceWorkerRegistration) {
  console.log('Showing update prompt');
  const event = new CustomEvent('worker:updated', {
    detail: registration,
  });
  document.dispatchEvent(event);
}

const ServiceWorkerPlugin: Plugin = async () => {
  console.log('Hello there from SW Plugin');

  if (!navigator.serviceWorker) {
    console.log('Service worker not supported.');
    return;
  }

  await navigator.serviceWorker.ready;

  const registration = await navigator.serviceWorker.getRegistration();

  if (!registration) {
    console.log('No service worker found.');
    return;
  }

  console.log('Registration found', registration);

  // Check for updates frequently (currently: every 5 minutes)
  setInterval(() => {
    console.log('Checking for service worker updates.');
    registration.update();
  }, 4000);

  if (registration.waiting) {
    updated(registration);
  }

  registration.onupdatefound = () => {
    console.log('update found');
    const { installing } = registration;

    if (!installing) {
      console.log('No installing service worker found.');
      return;
    }

    installing.onstatechange = () => {
      console.log('Installing service worker state changed', installing.state);
      if (installing.state === 'installed') {
        console.log('New service worker installed');
        if (navigator.serviceWorker.controller) {
          updated(registration);
        }
      }
    };
  };

  navigator.serviceWorker.addEventListener(
    'controllerchange',
    () => {
      window.location.reload();
    },
  );
};

export default ServiceWorkerPlugin;
