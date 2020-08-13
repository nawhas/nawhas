<template>
  <div>
    <v-snackbar
      v-model="show"
      :timeout="-1"
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
import { Component, Vue } from 'nuxt-property-decorator';

@Component
export default class UpdateServiceWorker extends Vue {
  private show = false;
  private worker: ServiceWorker|null = null;

  mounted() {
    // Register service worker listener.
    document.addEventListener('worker:updated', (e) => {
      this.showUpdateUi((e as CustomEvent<ServiceWorker>).detail);
    }, { once: true });
  }

  showUpdateUi(worker: ServiceWorker) {
    this.show = true;
    this.worker = worker;
  }

  refresh() {
    this.show = false;
    if (this.worker === null) {
      return;
    }

    this.worker.postMessage({ type: 'SKIP_WAITING' });
  }
}
</script>

<style lang="scss" scoped>

</style>
