<template>
  <page-header>
    <div class="d-flex align-center justify-center">
      <div class="header__icon">
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

export default Vue.extend({
  name: 'LibraryHeader',
});
</script>

<style scoped>
.header__icon {
  border: 1px solid white;
  width: 56px;
  height: 56px;
  border-radius: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 18px;
}
</style>
