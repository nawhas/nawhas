<template>
  <v-container class="app__section">
    <div v-if="status === 404" class="error-page error-page--404">
      <div class="error__illustration">
        <v-icon size="64">
          {{ mdiBinoculars }}
        </v-icon>
      </div>
      <h1 class="error__title">
        404
      </h1>
      <h2 class="error__subtitle">
        Oops! We couldn't find the page you're looking for.
      </h2>
      <v-btn to="/" x-large color="primary">
        Go home
      </v-btn>
    </div>
    <div v-else class="error-page error-page--other">
      <div class="error__illustration">
        <v-icon size="64">
          warning
        </v-icon>
      </div>
      <h1 class="error__title">
        Oops!
      </h1>
      <h2 class="error__subtitle">
        Something went wrong. We've been notified and we're working hard to fix it!
      </h2>
      <div class="error__actions">
        <v-btn to="/" x-large color="primary">
          Go home
        </v-btn>
        <v-btn x-large @click="refresh()">
          Refresh
        </v-btn>
      </div>
    </div>
  </v-container>
</template>

<script lang="ts">
import Vue from 'vue';
import { MetaInfo } from 'vue-meta';
import { NuxtError } from '@nuxt/types';
import { mdiBinoculars } from '@mdi/js';
import { generateMeta } from '@/utils/meta';

export default Vue.extend({
  layout: 'default',
  props: {
    error: {
      type: Object as () => NuxtError,
      default: null,
    },
  },
  data: () => ({
    mdiBinoculars,
  }),
  computed: {
    status(): number {
      return this.error.statusCode ?? 500;
    },
    title(): string {
      switch (this.status) {
        case 404: return '404 - Not Found';
      }
      return 'An error occurred.';
    },
  },
  methods: {
    refresh() {
      window.location.reload();
    },
  },
  head(): MetaInfo {
    return generateMeta({
      title: this.title,
    });
  },
});
</script>

<style scoped>
.error-page {
  min-height: calc(100vh - 100px);
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.error__illustration {
  width: 128px;
  height: 128px;
  border-radius: 128px;
  background: orangered;
  display: flex;
  align-items: center;
  justify-content: center;
}
.error__title {
  font-size: 96px;
  font-weight: 200;
  margin-bottom: 0;
  padding: 0;
}
.error__subtitle {
  font-size: 32px;
  font-weight: 200;
  margin-bottom: 32px;
}
</style>
