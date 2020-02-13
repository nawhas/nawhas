<template>
  <div class="track-hero__left">
    <div class="track-hero__avatar" :elevation="2">
      <v-avatar size="120px" class="white" tile>
        <v-skeleton-loader ref="skeleton" type="avatar" :tile="true" class="mx-auto"></v-skeleton-loader>
      </v-avatar>
    </div>
    <div class="track-hero__text">
      <h4 class="track-hero__title">
        <v-skeleton-loader width="150px" ref="skeleton" type="list-item" class="mx-auto"></v-skeleton-loader>
      </h4>
      <div class="track-hero__meta">
        <v-skeleton-loader ref="skeleton" type="list-item-three-line" class="mx-auto"></v-skeleton-loader>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'skeleton.tracks.header',
};
</script>
