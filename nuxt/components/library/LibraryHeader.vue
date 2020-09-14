<template>
  <page-header class="library-header">
    <div class="d-flex align-center justify-center">
      <div class="header__icon" :style="{ 'border-color': $vuetify.theme.dark ? 'white' : '#333' }">
        <v-icon size="32">
          local_library
        </v-icon>
      </div>
      Library
    </div>
  </page-header>
</template>

<script lang="ts">
import Vue from 'vue';
import PageHeader from '@/components/PageHeader.vue';

export default Vue.extend({
  name: 'LibraryHeader',

  components: {
    PageHeader,
  },
});
</script>

<style lang="scss" scoped>
.header__icon {
  border: 1px solid white;
  width: 56px;
  height: 56px;
  border-radius: 56px;
  display: flex;
  flex: none;
  align-items: center;
  justify-content: center;
  margin-right: 18px;
}
</style>
