<router>
  path: /library
  name: "library"
</router>

<template>
  <div class="library">
    <div class="icon outlined">
      <v-icon size="64">
        local_library
      </v-icon>
    </div>
    <h1 class="main-heading">
      Welcome to your library
    </h1>
    <h2 class="sub-heading">
      Add nawhas to your favorites, create playlists, and curate your own collection.
    </h2>
    <v-btn to="/" x-large color="white" class="black--text">
      Get Started
    </v-btn>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({

});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.library {
  background: linear-gradient(229.64deg, #F19100 -3.22%, #950900 90.48%);
  min-height: calc(100vh - 64px);
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.icon {
  width: 128px;
  height: 128px;
  border-radius: 128px;
  margin-bottom: 68px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.outlined {
  border: solid 1px white;
}

.main-heading {
  margin-bottom: 10px;
  font-size: 64px;
  font-weight: 200;
  padding: 0;
}

.sub-heading {
  font-size: 32px;
  font-weight: 200;
  margin-bottom: 32px;
}

@include breakpoint('md-and-down') {
  .library {
    min-height: calc(100vh - 56px);
  }

  .main-heading {
    font-size: 48px;
  }

  .sub-heading {
    font-size: 22px;
  }
}
</style>
