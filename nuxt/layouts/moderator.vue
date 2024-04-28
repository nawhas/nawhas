<template>
  <v-app :class="classes">
    <v-navigation-drawer
      v-model="drawer"
      permanent
      app
      clipped
      mini-variant
      expand-on-hover
      class="nav__drawer"
    >
      <v-list class="nav">
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
      color="primary"
      app
      clipped-left
      fixed
      elevate-on-scroll
      class="app-bar"
    >
      <div class="app-bar__left">
        <v-app-bar-nav-icon
          @click="$router.push('/')"
        >
          <v-icon>close</v-icon>
        </v-app-bar-nav-icon>
        <v-toolbar-title class="nav__title">
          Moderator Tools
        </v-toolbar-title>
      </div>
      <div class="app-bar__center" />
      <div class="app-bar__right">
        <user-menu class="user-menu" />
      </div>
    </v-app-bar>
    <v-main>
      <v-container
        fluid
        :class="{ 'main-container': true, 'grey lighten-5': !isDark }"
      >
        <nuxt />
      </v-container>
    </v-main>
    <audio-player />
    <update-service-worker />
    <toaster />
  </v-app>
</template>

<script lang="ts">
import Vue from 'vue';
import UserMenu from '@/components/navigation/UserMenu.vue';
import AudioPlayer from '@/components/audio-player/AudioPlayer.vue';
import Toaster from '@/components/utils/Toaster.vue';
import UpdateServiceWorker from '@/components/utils/UpdateServiceWorker.vue';
import { applyTheme } from '@/services/theme';

const links = [
  {
    icon: 'history',
    title: 'Revisions',
    exact: true,
    to: '/moderator/revisions',
  },
  {
    icon: 'lyrics',
    title: 'Draft Lyrics',
    exact: true,
    to: '/moderator/drafts/lyrics',
  },
  // {
  //   icon: 'group',
  //   title: 'Users',
  //   exact: true,
  //   to: '/moderator/users',
  // },
];

export default Vue.extend({
  name: 'DefaultLayout',

  components: {
    Toaster,
    UserMenu,
    AudioPlayer,
    UpdateServiceWorker,
  },

  middleware({ store, redirect }) {
    if (!store.getters['auth/isModerator']) {
      return redirect('/');
    }
  },

  data: () => ({
    drawer: true,
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

  watch: {
    '$store.state.auth.user': 'onAuthChange',
  },

  mounted() {
    applyTheme(this.$store.state.preferences.theme, this.$vuetify, this.$storage);
  },

  methods: {
    onAuthChange(value) {
      if (!value) {
        this.$router.replace('/');
      }
    },
  },
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.main-container {
  padding: 0;
  min-height: calc(100vh - 64px);
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

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .main-container {
    min-height: calc(100vh - 56px);
  }
}
</style>
