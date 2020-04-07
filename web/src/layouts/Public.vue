<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" temporary app>
      <v-list class="nav" shaped>
        <v-list-item
          v-for="link in navigation"
          :key="link.to"
          class="nav__tile"
          :to="link.to"
          active-class="nav__tile--active"
          :exact="link.exact"
        >
          <v-list-item-action class="nav__tile__action">
            <v-icon class="nav__tile__action__icon" v-text="link.icon"></v-icon>
          </v-list-item-action>
          <v-list-item-content class="nav__tile__content">
            <v-list-item-title class="nav__tile__content__title">{{ link.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar class="app-bar" :color="(!isDark) ? 'white' : null" app fixed elevate-on-scroll>
      <v-container class="app-bar__container">
        <div class="app-bar__left">
          <v-app-bar-nav-icon @click.native="drawer = !drawer" v-if="$vuetify.breakpoint.mdAndDown" />

          <v-toolbar-title class="nav__title">
            <router-link to="/" tag="div" class="masthead__logo">
              <logo-icon class="masthead__logo__icon" />
              <logo-wordmark :class="{ 'masthead__logo__wordmark': true, 'masthead__logo__wordmark--dark': isDark }" />
            </router-link>
          </v-toolbar-title>

          <nav class="nav__buttons" v-if="$vuetify.breakpoint.lgAndUp">
            <v-btn text v-for="(link) in navigation" class="nav__btn" :key="link.to" :to="link.to">
              {{ link.title }}
            </v-btn>
          </nav>
        </div>
        <div class="app-bar__center">
        </div>
        <div class="app-bar__right">
          <search />
          <user-menu class="user-menu" />
        </div>
      </v-container>
    </v-app-bar>
    <v-content>
      <v-container fluid :class="{ 'grey lighten-5': !isDark }" class="main-container">
        <router-view></router-view>
      </v-container>
    </v-content>
    <audio-player></audio-player>
    <update-service-worker />
  </v-app>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import Search from '@/components/search/Search.vue';
import navItems from '@/data/navigation';
import AudioPlayer from '@/components/audio-player/AudioPlayer.vue';
import UserMenu from '@/components/auth/UserMenu.vue';
import UpdateServiceWorker from '@/components/utils/UpdateServiceWorker.vue';
import LogoIcon from '@/assets/icon.svgx';
import LogoWordmark from '@/assets/wordmark.svgx';

@Component({
  components: {
    UpdateServiceWorker,
    Search,
    AudioPlayer,
    UserMenu,
    LogoIcon,
    LogoWordmark,
  },
})
export default class PublicVuetify extends Vue {
  private drawer: boolean | null = null;

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

  get navigation() {
    return navItems;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }
}
</script>

<style lang="scss" scoped>
@import "../styles/theme";

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
    > path {
      fill: $wordmark;
    }
  }
  &__wordmark--dark > path {
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
