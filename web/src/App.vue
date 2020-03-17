<template>
  <div id="app" :class="classes">
    <vue-progress-bar></vue-progress-bar>
    <router-view></router-view>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class App extends Vue {
  created() {
    this.$store.dispatch('auth/check');
    this.determineTheme();
  }
  get isPlayerShowing() {
    return this.$store.getters['player/track'] !== null;
  }
  get classes() {
    return {
      [`app--${this.$vuetify.breakpoint.name}`]: true,
      'app--player-showing': this.isPlayerShowing,
    };
  }
  determineTheme() {
    // This will check to see if the user has Dark Mode on their OS
    if (!window.matchMedia) {
      return;
    }
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      this.$vuetify.theme.dark = true;
    }
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      this.$vuetify.theme.dark = e.matches || false;
    });
  }
}
</script>

<style lang="scss">
@import "./styles/theme";

@import url('https://fonts.googleapis.com/css?family=Bellefair|Roboto+Condensed:300,300i,400,400i,700,700i|Roboto+Slab:100,300,400,700|Material+Icons|Material+Icons+Outlined');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

body.scroll--none {
  overflow: hidden;
  overscroll-behavior: contain;
}
// Layout Styles
.page-section {
  padding: 24px 48px;
}

.app__section {
  padding-bottom: 24px !important;
  .section__title {
    font-size: 1.4rem; // Overriding the font size.
    font-weight: map-deep-get($headings, 'h5', 'weight');
    line-height: map-deep-get($headings, 'h5', 'line-height');
    letter-spacing: map-deep-get($headings, 'h5', 'letter-spacing') !important;
    font-family: map-deep-get($headings, 'h5', 'font-family') !important;
    margin-bottom: 12px;
  }
  .section__title--with-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
}

.app--xs, .app--sm {
  .page-section {
    padding: 24px;
  }
  .app__section {
    padding: 24px;
  }
}

.app--player-showing {
  .main-container {
    padding-bottom: 80px !important;
  }
}

.vibrant-canvas {
  position: fixed;
  top: -10000px;
  left: -10000px;
  z-index: -1;
}
</style>
