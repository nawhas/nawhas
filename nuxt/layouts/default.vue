<template>
  <v-app :class="classes">
    <v-navigation-drawer
      v-model="drawer"
      temporary
      app
      class="nav__drawer"
    >
      <v-list class="nav" shaped>
        <v-list-item
          v-for="(link, i) in navigation"
          :key="i"
          :to="link.to"
          :exact="link.exact"
          active-class="nav__tile--active"
          class="nav__tile"
        >
          <v-list-item-action class="nav__tile__action">
            <v-icon class="nav__tile__action__icon" v-text="link.icon" />
          </v-list-item-action>
          <v-list-item-content class="nav__tile__content">
            <v-list-item-title class="nav__tile__content__title" v-text="link.title" />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar
      :color="(!isDark) ? 'white' : null"
      app
      fixed
      elevate-on-scroll
      class="app-bar"
    >
      <v-container class="app-bar__container">
        <div class="app-bar__left">
          <v-app-bar-nav-icon
            v-if="$vuetify.breakpoint.mdAndDown"
            @click.native="drawer = !drawer"
          >
            <v-icon>menu</v-icon>
          </v-app-bar-nav-icon>
          <v-toolbar-title class="nav__title">
            <nuxt-link to="/" tag="div" class="masthead__logo">
              <logo-icon class="masthead__logo__icon" />
              <logo-wordmark :class="{ 'masthead__logo__wordmark': true, 'masthead__logo__wordmark--dark': isDark }" />
            </nuxt-link>
          </v-toolbar-title>

          <nav v-if="$vuetify.breakpoint.lgAndUp" class="nav__buttons">
            <v-btn
              v-for="(link, i) in navigation"
              :key="i"
              text
              class="nav__btn"
              :to="link.to"
              v-text="link.title"
            />
          </nav>
        </div>
        <div class="app-bar__center" />
        <div class="app-bar__right">
          <global-search />
          <user-menu class="user-menu" />
        </div>
      </v-container>
    </v-app-bar>
    <v-main>
      <v-container
        fluid
        :class="{ 'main-container': true, 'grey lighten-5': !isDark }"
      >
        <nuxt />
      </v-container>
    </v-main>
    <v-footer app absolute>
      <span>&copy; {{ new Date().getFullYear() }}</span>
    </v-footer>
    <audio-player />
    <update-service-worker />
    <toaster />
  </v-app>
</template>

<script lang="ts">
import Vue from 'vue';
import UserMenu from '@/components/navigation/UserMenu.vue';
import AudioPlayer from '@/components/audio-player/AudioPlayer.vue';
import GlobalSearch from '@/components/search/GlobalSearch.vue';
import Toaster from '@/components/utils/Toaster.vue';
import UpdateServiceWorker from '@/components/utils/UpdateServiceWorker.vue';
import { applyTheme } from '@/services/theme';

const LogoIcon = require('@/assets/svg/icon.svg?inline');
const LogoWordmark = require('@/assets/svg/wordmark.svg?inline');

const links = [
  {
    icon: 'home',
    title: 'Home',
    exact: true,
    to: '/',
  },
  {
    icon: 'pageview',
    title: 'Browse',
    exact: false,
    to: '/reciters',
  },
  {
    icon: 'info',
    title: 'About',
    exact: false,
    to: '/about',
  },
];

export default Vue.extend({
  name: 'DefaultLayout',

  components: {
    LogoWordmark,
    LogoIcon,
    Toaster,
    UserMenu,
    AudioPlayer,
    GlobalSearch,
    UpdateServiceWorker,
  },

  async fetch() {
    await Promise.all([
      this.$store.dispatch('auth/check').catch(() => null),
      this.$store.dispatch('features/fetch').catch(() => null),
    ]);
  },

  data: () => ({
    drawer: false,
  }),

  computed: {
    mobile() {
      return this.$vuetify.breakpoint.smAndDown;
    },
    isDark() {
      return this.$vuetify.theme.dark;
    },
    navigation: () => [
      ...links,
    ],
    classes() {
      return {
        [`app--${this.$vuetify.breakpoint.name}`]: true,
        'app--player-showing': this.$store.getters['player/track'] !== null,
      };
    },
  },

  mounted() {
    applyTheme(this.$store.state.preferences.theme, this.$vuetify, this.$storage);
  },
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.main-container {
  padding: 0;
  min-height: calc(100vh - 64px);
}

.nav__tile__action {
  justify-content: center;
}

.app-bar__right {
  align-self: flex-start;
}

.nav__tile--active {
  .nav__tile__action {
    color: var(--v-primary-base);
  }

  .nav__tile__content {
    font-weight: bold;
    color: var(--v-primary-base);
  }
}
.masthead__logo {
  height: 38px;
  cursor: pointer;
  display: flex;
  align-items: center;

  &__icon {
    height: 38px;
  }
  &__wordmark {
    height: 16px;
    margin-left: 8px;
    ::v-deep g g {
      fill: $wordmark;
    }
  }
  &__wordmark--dark ::v-deep g g {
    fill: rgba(255, 255, 255, 0.93);
  }
}
.nav__btn {
  text-transform: none;
  font-weight: 400;
}
.nav__title {
  margin-right: 12px;
}

.app-bar__container {
  display: flex;
  align-items: center;
  padding: 4px 0;
  margin: 0 auto;
  position: relative;
  height: 64px;
}
.app-bar__left, .app-bar__right {
  flex: 1;
  display: flex;
  align-items: center;
}
.app-bar__left {
  justify-content: flex-start;
}
.app-bar__right {
  justify-content: flex-end;
}
.user-menu {
  align-self: flex-start;
  margin: 4px 0 0 4px;
}
.nav__drawer {
  z-index: 500;
}

@media #{map-get($display-breakpoints, 'md-and-down')} {
  .app-bar__left {
    flex: 0;
  }
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .main-container {
    min-height: calc(100vh - 56px);
  }

  .user-menu {
    margin: 0;
    align-self: center;
  }

  .app-bar__right {
    align-self: center;
  }
}
</style>
