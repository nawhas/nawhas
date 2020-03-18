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
    <v-app-bar class="app-bar" app fixed elevate-on-scroll>
      <div class="app-bar__left">
        <nav class="nav__buttons" v-if="$vuetify.breakpoint.lgAndUp">
          <v-btn text v-for="(link) in navigation" class="nav__btn" :key="link.to" :to="link.to">
            {{ link.title }}
          </v-btn>
        </nav>
        <v-app-bar-nav-icon @click.native="drawer = !drawer" v-else />
      </div>
      <div class="app-bar__center">
        <v-toolbar-title class="nav__title">
          <router-link to="/" tag="div" class="masthead__logo">
            <img v-if="isDark" class="masthead__logo"
                 :src="require('../assets/logo.dark.svg')"
                 alt="Nawhas.com"
            />
            <img v-else class="masthead__logo"
                 :src="require('../assets/logo.svg')"
                 alt="Nawhas.com"
            />
          </router-link>
        </v-toolbar-title>
      </div>
      <div class="app-bar__right nav__search">
        <search />
        <user-menu class="user-menu" />
      </div>
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

@Component({
  components: {
    UpdateServiceWorker,
    Search,
    AudioPlayer,
    UserMenu,
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
@import "~vuetify/src/styles/styles";

.main-container {
  padding: 0;
  min-height: calc(100vh - 64px);
}

.nav__tile__action {
  justify-content: center;
}

.nav__search {
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
}
.nav__btn {
  text-transform: none;
  font-weight: 400;
}
.nav__title {
  margin: auto;
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
}
</style>
