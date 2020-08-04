<template>
  <div>
    <v-snackbar
      v-model="show"
      :timeout="0"
      :top="$vuetify.breakpoint.mdAndUp"
      right
      multi-line
    >
      An new version of Nawhas.com is available.
      <v-btn color="primary lighten-2" dark text @click="refresh">
        Update
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class UpdateServiceWorker extends Vue {
  private show = false;
  private registration: ServiceWorkerRegistration|null = null;

  created() {
    // Register service worker listener.
    document.addEventListener('worker:updated', (e) => {
      this.showUpdateUi((e as CustomEvent<ServiceWorkerRegistration>).detail);
    }, { once: true });

    navigator.serviceWorker.addEventListener(
      'controllerchange',
      () => {
        window.location.reload();
      },
    );
  }

  showUpdateUi(registration: ServiceWorkerRegistration) {
    this.show = true;
    this.registration = registration;
  }

  refresh() {
    this.show = false;
    if (this.registration === null) {
      return;
    }

    // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
    this.registration.waiting!.postMessage({ type: 'SKIP_WAITING' });
  }
}
</script>

<style lang="scss" scoped>

</style>
