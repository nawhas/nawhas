<template>
  <v-card class="lyrics">
    <v-card-title class="card__title subtitle-1">
      <v-icon class="card__title__icon material-icons-outlined">
        speaker_notes
      </v-icon>
      <div>Write-Up</div>
      <v-spacer />
      <v-tooltip v-if="synchronized" top>
        <template #activator="{ on }">
          <v-icon class="card__title__right-icon" color="accent" v-on="on">
            assistant
          </v-icon>
        </template>
        <span>New! Write-up synchronized with audio</span>
      </v-tooltip>
    </v-card-title>
    <v-card-text class="lyrics__content" :class="{ 'black--text': !$vuetify.theme.dark }">
      <template v-if="track">
        <lyrics-renderer class="lyrics__renderer" :track="track" />
      </template>
      <div v-else class="lyrics__content__loader">
        <lyrics-skeleton />
      </div>
    </v-card-text>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';
import LyricsSkeleton from '@/components/loaders/LyricsSkeleton.vue';
import { Documents, Lyrics, Format } from '@/entities/lyrics';
import JsonV1Document = Documents.JsonV1.Document;

@Component({
  components: {
    LyricsRenderer,
    LyricsSkeleton,
  },
})
export default class LyricsCard extends Vue {
  @Prop({ type: Object }) private readonly track!: any;

  get synchronized(): boolean {
    if (!this.track || !this.track.lyrics) {
      return false;
    }

    const model: Lyrics = this.track.lyrics;

    if (model.format !== Format.JsonV1) {
      return false;
    }

    const lyrics: JsonV1Document = JSON.parse(model.content);

    return lyrics.meta.timestamps;
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';
@import '~assets/tracks/cards';

.lyrics {
  padding: 0;
}

.lyrics__content {
  padding: 16px 0 24px;
  font-weight: 400;
  font-size: 1.1rem;
  line-height: 2.3rem;
}

.lyrics__content__loader {
  padding: 16px;
}

.lyrics__renderer ::v-deep  .lyrics__empty {
  font-family: 'Roboto Slab', sans-serif;
  display: flex;
  justify-content: center;
  color: rgba(17, 13, 13, 0.3);
  font-size: 20px;
  font-weight: 300;
  padding: 60px 30px;
  text-align: center;
  flex-direction: column;

  .lyrics__empty-message {
    display: flex;
    margin: auto;
    align-self: center;
  }
  .lyrics__empty-actions {
    padding-top: 16px;
  }
}

.lyrics__renderer ::v-deep .lyrics__plain-text {
  padding: 0 24px;
}
.lyrics__renderer ::v-deep .lyrics__group {
  display: flex;
  border-left: 3px solid transparent;
  padding: 3px 24px;
  color: rgba(0, 0, 0, 0.76);

  &.lyrics__group--highlighted {
    border-left: 3px solid $primary;
    background-color: rgba(0, 0, 0, 0.07);
    color: black;
  }

  .lyrics__group__timestamp {
    font-family: 'Roboto Mono', monospace;
    color: rgba(0, 0, 0, 0.5);
    width: 45px;
    margin-right: 16px;
    text-align: right;
    font-size: 14px;
  }

  .lyrics__group__lines {
    .lyrics__group__lines__line {
      display: flex;
      align-items: center;
    }
  }

  .lyrics__spacer {
    width: 1px;
    height: 12px;
  }

  .lyrics__repeat {
    margin-left: 8px;
    padding: 5px 8px;
    text-align: center;;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Roboto Mono', monospace;
    font-weight: 600;
    line-height: 14px;
    border: 1px solid rgba(0,0,0,0.6);
  }
}

.lyrics__renderer.lyrics--dark {
  ::v-deep .lyrics__empty {
    color: rgba(255, 255, 255, 0.7);
  }
  ::v-deep .lyrics__group {
    color: rgba(255, 255, 255, 0.76);

    &.lyrics__group--highlighted {
      color: white;
      border-left: 3px solid $primary;
      background-color: rgba(255, 255, 255, 0.07);
    }

    .lyrics__group__timestamp {
      color: #a6a6a6;
    }
    .lyrics__repeat {
      border-color: rgba(255,255,255,0.76);
    }
  }
}
</style>
