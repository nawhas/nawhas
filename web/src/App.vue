<template>
  <div id="app" :class="'app--' + $vuetify.breakpoint.name">
    <vue-progress-bar></vue-progress-bar>
    <router-view></router-view>
  </div>
</template>

<script>
import Vue from 'vue';

export default Vue.extend({
  name: 'App',
});
</script>

<style lang="scss">
@import url('https://fonts.googleapis.com/css?family=Bellefair|Roboto+Condensed:300,300i,400,400i,700,700i|Roboto+Slab:100,300,400,700');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

// Layout Styles
.page-section {
  padding: 24px 48px;
}

.app--xs {
  .page-section {
    padding: 24px;
  }
}

.vibrant-canvas {
  position: fixed;
  top: -10000px;
  left: -10000px;
  z-index: -1;
}
</style>
