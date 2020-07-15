<template>
  <v-container class="app__section moderator-layout">
    <div class="moderator-layout__nav">
      <v-list nav dense class="pa-0" color="transparent">
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
    </div>
    <div class="moderator-layout__content">
      <router-view />
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
.moderator-layout {
  display: flex;
  align-items: flex-start;
  padding-top: 24px;
  width: 100%;
  overflow: hidden;
}
.moderator-layout__nav {
  min-width: 250px;
  margin-right: 24px;
}
.moderator-layout__content {
  flex: 1 1;
  max-width: calc(100% - 250px - 24px);
}
</style>
