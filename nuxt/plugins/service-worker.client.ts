/* eslint-disable no-console */
import { Plugin } from '@nuxt/types';
import { Workbox } from 'workbox-window';

declare global {
  interface Window {
    $workbox: Promise<Workbox>;
  }
}

const ServiceWorkerPlugin: Plugin = async () => {
  const workbox = await window.$workbox;

  if (workbox) {
    workbox.addEventListener('waiting', (e) => {
      // If we don't do this we'll be displaying the notification after the initial installation, which isn't perferred.
      if (e.isUpdate) {
        console.log('New service worker found, but is stuck in waiting.');
        const event = new CustomEvent<ServiceWorker>('worker:updated', {
          detail: e.sw,
        });
        document.dispatchEvent(event);

        workbox.addEventListener('controlling', () => window.location.reload());
      }
    });
  }
};

export default ServiceWorkerPlugin;
