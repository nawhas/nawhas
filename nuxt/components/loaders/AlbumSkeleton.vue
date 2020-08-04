<template>
  <v-card class="album-skeleton">
    <div class="album-skeleton__header">
      <v-skeleton-loader
        type="avatar"
        tile
        :width="artworkSize"
        :height="artworkSize"
        :class="{ 'album-skeleton__artwork': true, 'album-skeleton__artwork--dark': isDark }"
      />
      <div class="album-skeleton__details">
        <h5 class="album-skeleton__title">
          <v-skeleton-loader type="text" min-width="200px" min-height="24px" class="mt-1" />
        </h5>
        <h6 class="album-skeleton__release-date">
          <v-skeleton-loader type="text" max-width="120px" />
        </h6>
      </div>
    </div>
    <div class="album-skeleton__tracks">
      <div v-for="index in 6" :key="index" class="album-skeleton__track">
        <v-skeleton-loader type="text" :max-width="`${Math.random() * 400}px`" min-width="100px" />
      </div>
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class AlbumSkeleton extends Vue {
  get artworkSize() {
    if (this.$vuetify.breakpoint.smAndDown) {
      return 48;
    }
    return 128;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }
}
</script>

<style lang="scss">
@import '~assets/theme';

.album-skeleton {
  margin-top: 90px;
}

.album-skeleton__header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-bottom: 20px;
}

.album-skeleton__artwork {
  margin-top: -48px;
  margin-left: 20px;
  border: 5px solid white;
  float: left;
  overflow: hidden;
  box-sizing: content-box;
  @include elevation(3);
  background: white;

  > div {
    width: 100%;
    height: 100%;
  }

  &.album-skeleton__artwork--dark {
    background: #1E1E1E;
    border-color: #bbb;
  }
}

.album-skeleton__details {
  padding: 24px;
  color: white;
  flex-grow: 1;
}

.album-skeleton__title {
  margin: 0 0 8px 0;
  padding: 0;
}

.album-skeleton__release-date {
  margin: 0;
  padding: 0;
}

.album-skeleton__track {
  padding: 18px 16px 11px 16px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .album-skeleton {
    margin-top: 0;
    margin-bottom: 24px;
  }
  .album-skeleton__header {
    display: flex;
    align-items: center;
    flex-direction: row;
    padding-bottom: 0;
  }
  .album-skeleton__details {
    margin: 0;
    padding: 16px;
  }
  .album-skeleton__artwork {
    float: none;
    margin: 16px 0 16px 16px;
    @include elevation(0);
  }
  .album-skeleton__title {
    margin: 0;
  }
}
</style>
