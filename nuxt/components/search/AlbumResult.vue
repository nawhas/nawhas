<template>
  <nuxt-link :to="album.meta.url" class="link">
    <div :class="{ 'album-result': true, 'album-result--dark': isDark }">
      <v-avatar tile size="40" class="album-result__artwork">
        <img ref="avatarElement" crossorigin :src="image" :alt="album.title">
      </v-avatar>
      <div class="album-result__text">
        <div class="album-result__name body-1" :title="album.title">
          <div class="album-result__title" v-html="highlighted.title" />
          <div class="album-result__year body-2" v-html="highlighted.year" />
        </div>
        <div class="album-result__reciter body-2" :title="album.reciter" v-html="highlighted.reciter" />
      </div>
    </div>
  </nuxt-link>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';

interface AlbumSearchResult {
  // Album UUID
  id: string;
  // Album Title
  title: string;
  // Album Year
  year: string;
  // Reciter Name
  reciter: string;
  // Metadata
  meta: {
    artwork: string|null;
    url: string;
  };
  _formatted?: Formatted;
}

type Formatted = Omit<AlbumSearchResult, '_formatted'>;

@Component
export default class AlbumResult extends Vue {
  @Prop({ type: Object, required: true }) private readonly album!: AlbumSearchResult;

  get highlighted(): Formatted {
    return this.album._formatted ?? this.album;
  }

  get image() {
    return this.album.meta.artwork || require('@/assets/img/defaults/default-album-image.png');
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }
};
</script>

<style lang="scss">
.link {
  text-decoration: none;
}
.album-result {
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

  .album-result__artwork {
    border: 1px solid #eee;
  }

  .album-result__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: 100%;
    color: rgba(0,0,0,0.67);

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
    }
  }
}

.album-result--dark {
  .album-result__artwork {
    border: 1px solid white;
  }
  .album-result__text {
    color: white;
    em, mark {
      color: wheat;
    }
  }
}
</style>
