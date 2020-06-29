<template>
  <v-container>
    <div class="moderator">
      <v-card max-width="300" tile>
        <v-list class="pa-0">
          <v-list-item-group v-model="item" color="primary">
            <v-list-item v-for="(item, i) in items" :key="i">
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
      <v-content class="moderator-content">
        <div>Content goes here</div>
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
    { text: 'Revision History', icon: 'history' },
    { text: 'Users', icon: 'people' },
  ];
}
</script>

<style lang="scss" scoped>
.moderator {
  display: flex;
}
</style>
