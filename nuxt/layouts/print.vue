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
    const handler = () => {
      window.close();
    };
    this.$el['__onPrintCompleteHandler__'] = handler;
    window.addEventListener('afterprint', handler);

    this.triggerPrint();
  },

  beforeDestroy() {
    window.removeEventListener('afterprint', this.$el['__onPrintCompleteHandler__']);
    delete this.$el['__onPrintCompleteHandler__'];
  },

  methods: {
    triggerPrint() {
      window.clearTimeout(this.timeout);
      window.timeout = window.setTimeout(() => {
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
