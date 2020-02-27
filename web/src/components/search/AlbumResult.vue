<template>
  <router-link :to="album.url" tag="div">
    <div class="album-result">
      <div class="album-result__artwork">
        <v-avatar tile size="40">
          <img crossorigin ref="avatarElement" :src="image" :alt="album.title" />
        </v-avatar>
      </div>
      <div class="album-result__text">
        <div class="album-result__name body-1" :title="album.title">
          <div class="album-result__title">
            <ais-highlight :hit="album" attribute="title" />
          </div>
          <div class="album-result__year body-2">
            <ais-highlight :hit="album" attribute="year" />
          </div>
        </div>
        <div class="album-result__reciter body-2" :title="album.reciter.name">
          <ais-highlight :hit="album" attribute="reciter.name" />
        </div>
      </div>
    </div>
  </router-link>
</template>

<script>
export default {
  name: 'AlbumResult',
  props: ['album'],
  computed: {
    image() {
      return this.album.artwork || '/img/default-album-image.png';
    },
  },
};
</script>

<style lang="scss">
.album-result {
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

  .album-result__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: 100%;

    em, mark {
      font-style: normal;
      background: none;
      padding-bottom: 1px;
      border-bottom: 2px solid orangered;
    }
    .album-result__name {
      justify-content: space-between;
      align-items: baseline;
      display: flex;
      width: 100%;
    }
    .album-result__year {
      display: block;
      color: rgba(0,0,0,0.67);
    }
    .album-result__reciter {
      color: rgba(0,0,0,0.67);
    }
  }

  .album-result__artwork {
    border: 2px solid white;
    overflow: hidden;
  }
}
</style>
