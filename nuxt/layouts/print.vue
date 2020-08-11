<template>
  <v-app>
    <v-main class="content">
      <router-view />
    </v-main>
  </v-app>
</template>

<script lang="ts">
/* eslint-disable dot-notation */
import Vue from 'vue';

interface Data {
  timeout: number | undefined;
}

export default Vue.extend({
  name: 'PrintLayout',

  data(): Data {
    return {
      timeout: undefined,
    };
  },

  mounted() {
    this.triggerPrint();
  },

  methods: {
    triggerPrint() {
      window.clearTimeout(this.timeout);
      this.timeout = window.setTimeout(() => {
        window.print();
      }, 500);
    },
  },
});
</script>

<style lang="scss">
.content {
  background: white !important;
  color: black !important;
  padding: 24px !important;
}

@media print {
  .content {
    padding: 0 !important;
  }
}
</style>
