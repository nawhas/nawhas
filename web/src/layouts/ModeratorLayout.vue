<template>
  <v-container>
    <div class="moderator">
      <v-card max-width="300">
        <v-list class="pa-0">
          <v-list-item-group v-model="item" color="primary">
            <v-list-item v-for="(item, i) in items" :key="i" :to="{ name: item.linkName }">
              <v-list-item-icon>
                <v-icon v-text="item.icon"></v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title v-text="item.text"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-card>
      <v-content class="moderator-content pa-0">
        <router-view />
      </v-content>
    </div>
  </v-container>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class ModeratorLayout extends Vue {
  private item = 0;
  private items: Array<object> = [
    {
      text: 'Revision History',
      icon: 'history',
      linkName: 'ModeratorRevisionHistory',
    },
    { text: 'Users', icon: 'people', linkName: 'ModeratorUsers' },
  ];
}
</script>

<style lang="scss" scoped>
.moderator {
  display: flex;
  margin-top: 20px;
}
.moderator-content {
  margin-left: 20px;
}
</style>
