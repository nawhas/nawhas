<template>
  <div id="app" :class="classes">
    <vue-progress-bar></vue-progress-bar>
    <router-view></router-view>
  </div>
</template>

<script lang="ts">
import { Component, Watch, Vue } from 'vue-property-decorator';

@Component
export default class App extends Vue {
  created() {
    this.$store.dispatch('auth/check');
    this.determineTheme();
    this.installThemeEventListener();
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

  get theme() {
    return this.$store.state.preferences.theme;
  }

  @Watch('theme')
  onThemeChanged() {
    this.determineTheme();
  }

  determineTheme() {
    const preference = this.theme;

    if (preference === 'dark') {
      this.$vuetify.theme.dark = true;
      return;
    }

    if (preference === 'light') {
      this.$vuetify.theme.dark = false;
      return;
    }

    if (preference === 'auto' && window.matchMedia) {
      // This will check to see if the user has Dark Mode on their OS
      this.$vuetify.theme.dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
  }

  installThemeEventListener() {
    if (!window.matchMedia) {
      return;
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      this.determineTheme();
    });
  }
}
</script>

<style lang="scss">
@import "./styles/theme";

@import url('https://fonts.googleapis.com/css?family=Bellefair|Roboto+Condensed:300,300i,400,400i,700,700i|Roboto+Slab:100,300,400,700|Roboto+Mono:100,300,400,700|Material+Icons|Material+Icons+Outlined');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

.app__section {
  padding-bottom: 24px !important;
  max-width: 1000px;

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

@media (prefers-color-scheme: dark) {
  body, html {
    background-color: map-deep-get($material-dark, 'background');
  }
}
</style>
