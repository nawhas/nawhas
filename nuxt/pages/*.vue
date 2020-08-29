<template>
  <div />
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  name: 'HandleNotFound',

  created() {
    console.log('hello from not found page');
    return Promise.resolve();
  },
});
</script>

<style lang="scss" scoped>

</style>
