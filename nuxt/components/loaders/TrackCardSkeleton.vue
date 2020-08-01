<template>
  <v-card class="track-skeleton">
    <div class="track-skeleton__text">
      <div class="track-skeleton__name body-2">
        <v-skeleton-loader type="text" />
      </div>
      <div class="track-skeleton__album caption">
        <v-skeleton-loader type="text" max-width="120px" />
      </div>
      <div class="track-skeleton__reciter-year caption">
        <v-skeleton-loader type="text" max-width="90px" />
      </div>
    </div>
    <div class="track-skeleton__album-art">
      <v-skeleton-loader type="avatar" tile width="96px" height="96px" />
    </div>
  </v-card>
</template>

<style lang="scss">
@import "../../assets/theme";

.track-skeleton {
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .track-skeleton__text {
    padding: 16px 16px 16px 24px;
    flex-grow: 1;
    @include transition(color);

    .track-skeleton__name {
      margin: 3px 0;
    }
  }

  .track-skeleton__album-art {
    > div, > div > div {
      width: 100%;
      height: 100%;
    }
  }
}
</style>
