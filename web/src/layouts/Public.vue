<template>
  <v-app id="keep">
    <v-navigation-drawer v-model="drawer" fixed clipped floating width="250" app>
      <div v-for="(item, index) in navigation" :key="item.group">
        <v-list class="nav" shaped>
          <v-list-item
            class="nav__tile"
            v-for="link in item.children"
            :key="link.to"
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
        <v-divider v-if="index < navigation.length - 1"></v-divider>
      </div>
    </v-navigation-drawer>
    <v-app-bar class="app-bar" color="white" app fixed clipped-left flat>
      <v-app-bar-nav-icon @click.native="drawer = !drawer" v-show="$vuetify.breakpoint.mdAndDown" />
      <v-toolbar-title class="d-flex pa-4 nav__title">
        <router-link to="/" tag="div" class="masthead__logo">
          <img class="masthead__logo"
               :src="require('../assets/logo.svg')"
               alt="Nawhas.com"
          />
        </router-link>
      </v-toolbar-title>
      <v-spacer v-if="mobile"></v-spacer>
      <div class="nav__search">
        <search />
      </div>
    </v-app-bar>
    <v-content>
      <v-container fluid class="grey lighten-5 main-container">
        <router-view></router-view>
      </v-container>
    </v-content>
  </v-app>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import Search from '@/components/search/Search.vue';
import navItems from '@/data/navigation';

@Component({
  components: {
    Search,
  },
})
export default class PublicVuetify extends Vue {
  private items = navItems;

  private drawer: boolean | null = null;

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

  get navigation() {
    // return filtered nav list based on role
    const items: object[] = [];
    const role = this.$store.getters['auth/userRole'];

    for (const group of this.items) {
      group.children = group.children.filter(
        (child) => !(child.role && child.role !== role),
      );

      if (group.children.length > 0) {
        items.push(group);
      }
    }

    return items;
  }
}
</script>

<style lang="scss" scoped>
.main-container {
  padding: 0;
}

.nav__tile__action {
  justify-content: center;
}

.nav__title {
  min-width: 232px;
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
</style>
