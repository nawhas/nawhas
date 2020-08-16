<router>
  path: /library
  name: "library"
</router>

<template>
  <div class="library justify-center align-center">
    <div class="icon">
      <v-icon>
        local_library
      </v-icon>
    </div>
    <div class="main-heading">
      Welcome to your library
    </div>
    <div class="sub-heading">
      Add nawhas to your favorites, create playlists, and curate your own collection.
    </div>
    <div class="getting-started">
      <v-btn>Get Started</v-btn>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({

});
</script>

<style>
  .library {
    background-color: #F19100;
    min-height: calc(100vh - 64px);
    display: flex;
  }
</style>
