<template>
  <v-container>
    <div class="moderator">
      <v-list nav dense class="side-nav pa-0">
        <v-list-item-group color="primary">
          <v-list-item v-for="(item, i) in nav" :key="i" :to="{ name: item.to }" exact>
            <v-list-item-icon>
              <v-icon v-text="item.icon"></v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="item.text"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
      <v-content class="moderator-content pa-0">
        <router-view/>
      </v-content>
    </div>
  </v-container>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

interface NavItem {
  text: string;
  icon: string;
  to: string;
}
@Component
export default class ModeratorLayout extends Vue {
  get nav(): Array<NavItem> {
    return [
      {
        text: 'Revision history',
        icon: 'history',
        to: 'moderator.history',
      },
      {
        text: 'Users',
        icon: 'people',
        to: 'moderator.users',
      },
    ];
  }
}
</script>

<style lang="scss" scoped>
.moderator {
  display: flex;
  margin-top: 20px;
}

.moderator-content {
  margin-left: 32px;
}

.side-nav {
  background-color: transparent;
  min-width: 250px;
}
</style>
