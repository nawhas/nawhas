<template>
  <router-link :to="track.url" class="link">
    <div class="track-result">
      <div class="track-result__artwork">
        <v-avatar tile size="40">
          <img crossorigin :src="image" :alt="track.title" />
        </v-avatar>
      </div>
      <div class="track-result__text">
        <div class="track-result__name body-1" :title="track.title">
          <div class="track-result__title">
            <ais-highlight :hit="track" attribute="title" />
          </div>
          <div class="track-result__year body-2">
            <ais-highlight :hit="track" attribute="album.year" />
          </div>
        </div>
        <div class="track-result__reciter body-2">
          <ais-highlight :hit="track" attribute="reciter.name" />
        </div>
        <div class="track-result__lyrics body-2">
          <ais-snippet v-if="track.lyrics" :hit="track" attribute="lyrics" />
          <span v-else :hit="track">No lyrics available</span>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script>
export default {
  name: 'TrackResult',
  props: ['track'],
  computed: {
    image() {
      return this.track.album.artwork || '/img/default-album-image.png';
    },
  },
};
</script>

<style lang="scss">
.link {
  text-decoration: none;
}
.track-result {
  color: rgba(0, 0, 0, 0.76);
  padding: 8px 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  will-change: background-color;
  background-color: rgba(0,0,0,0.0);
  transition: background-color 280ms;

  &:hover {
    background-color: rgba(0,0,0,0.08);
  }

  .track-result__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: 100%;

    em, mark {
      font-style: inherit;
      background: none;
      padding-bottom: 1px;
      border-bottom: 2px solid orangered;
    }
    .track-result__name {
      justify-content: space-between;
      align-items: baseline;
      display: flex;
      width: 100%;
    }
    .track-result__year {
      display: block;
      color: rgba(0,0,0,0.67);
    }
    .track-result__reciter {
      color: rgba(0,0,0,0.67);
      margin-bottom: 2px;
    }
    .track-result__lyrics {
      color: rgba(0,0,0,0.67);
      font-style: italic;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
  }
}
</style>
