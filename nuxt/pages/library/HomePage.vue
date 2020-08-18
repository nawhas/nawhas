<router>
  path: /library/home
  name: "LibraryHomePage"
</router>

<template>
  <div>
    <page-header>
      <v-icon size="64">
        local_library
      </v-icon>
      Library
    </page-header>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import PageHeader from '@/components/PageHeader';

export default Vue.extend({
  components: {
    PageHeader,
  },
});
</script>

<style scoped>

</style>
