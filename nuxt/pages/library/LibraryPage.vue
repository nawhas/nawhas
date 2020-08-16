<router>
  path: /library
  name: "library"
</router>

<template>
  <div class="library justify-center align-center">
    <div class="icon">
      <v-avatar class="outlined" size="80">
        <v-icon size="50">
          local_library
        </v-icon>
      </v-avatar>
    </div>
    <div class="main-heading">
      <h1 class="display-1 font-weight-thin mb-4">
        Welcome to your library
      </h1>
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
  background: linear-gradient(229.64deg, #F19100 -3.22%, #950900 90.48%);
  min-height: calc(100vh - 64px);
  display: flex;
  flex-direction: column;
}

.icon {
  margin-bottom: 68px;
}

.outlined {
  border: solid 1px white;
}

.main-heading {
  margin-bottom: 10px;
}

.sub-heading {
  margin-bottom: 60px;
}
</style>
