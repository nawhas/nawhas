<template>
  <div>
    <v-snackbar
      v-model="snackbar"
      top
      right
      multi-line
    >
      <div class="snackbar__content d-flex align-center justify-start">
        <v-icon
          v-if="toast.icon"
          :color="color"
          class="mr-2"
        >
          {{ toast.icon }}
        </v-icon>
        {{ toast.text }}
      </div>

      <template #action="{ attrs }">
        <v-btn
          color="accent"
          dark
          text
          v-bind="attrs"
          @click="snackbar = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'nuxt-property-decorator';
import { EventBus } from '@/events';
import { TOAST_SHOW, ToastOptions } from '@/events/toaster';

@Component
export default class Toaster extends Vue {
  private snackbar = false;
  private toast: ToastOptions = {
    text: '',
  };

  get color(): string|undefined {
    switch (this.toast.type) {
      case 'success': return 'green';
      default: return undefined;
    }
  }

  mounted() {
    EventBus.$on(TOAST_SHOW, (toast: ToastOptions) => {
      this.snackbar = true;
      this.toast = toast;
    });
  }

  beforeDestroy() {
    EventBus.$off(TOAST_SHOW);
  }
}
</script>
