<template>
  <div>
    <v-snackbar
        top
        right
        v-model="snackbar"
    >
      {{ toast.text }}
      <v-btn dark text @click="snackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { EventBus } from '@/events';
import { TOAST_SHOW, ToastOptions } from '@/events/toaster';

@Component
export default class Toaster extends Vue {
  private snackbar = false;
  private toast: ToastOptions = {
    text: '',
  };

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
