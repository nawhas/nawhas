<template>
  <v-card :class="classes" outlined>
    <div class="draft-lyrics-card__content">
      <div class="draft-lyrics-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon>{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="draft-lyrics-card__text">
        <div class="draft-lyrics-card__title body-1">
          <div class="mr-1">
            {{ title }}
          </div>
          <div v-if="subtitle" class="body-2 text--disabled" v-text="subtitle" />
          <v-btn
            small
            icon
            :to="goToTrack"
            target="_blank"
          >
            <v-icon small>
              open_in_new
            </v-icon>
          </v-btn>
        </div>
      </div>
      <div class="draft-lyrics-card__text draft-lyrics-card__text-right">
        <div class="draft-lyrics-card__name caption" :title="draftLyric.updatedAt | localDate">
          {{ draftLyric.updatedAt | relativeDate }}
        </div>
        <div v-if="createdBy" class="draft-lyrics-card__name caption">
          by {{ createdBy }}
        </div>
      </div>
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { relative as relativeDate, local as localDate } from '@/filters/date';
import { ChangeType, getUserDisplay } from '@/entities/revision';
import DiffTable from '@/components/moderator/revisions/DiffTable.vue';
import { DraftLyrics } from '@/entities/lyrics';
import { getTrackUri } from '@/entities/track';

@Component({
  filters: {
    relativeDate,
    localDate,
  },
  components: {
    DiffTable,
  },
})
export default class DraftLyricsCard extends Vue {
  @Prop({ type: Object, required: true }) private readonly draftLyric!: DraftLyrics;
  private readonly ChangeType = ChangeType;
  private readonly getUserDisplay = getUserDisplay;

  get classes() {
    return {
      'draft-lyrics-card': true,
      'draft-lyrics-card--dark': this.$vuetify.theme.dark,
    };
  }

  get title() {
    return this.draftLyric.track?.title;
  }

  get subtitle() {
    return `(${this.draftLyric.track?.reciter?.name} • ${this.draftLyric.track?.year})`;
  }

  get icon(): string {
    return 'lyrics';
  }

  get createdBy(): string {
    // TODO: Change to display the person who updated the record last
    return '';
  }

  get goToTrack(): string {
    if (this.draftLyric.track === undefined || this.draftLyric.track.reciter === undefined) {
      return '';
    }
    return getTrackUri(this.draftLyric.track, this.draftLyric.track.reciter);
  }
}
</script>

<style lang="scss">
@import '~assets/theme';

.draft-lyrics-card {
  margin-bottom: 10px;
  background-color: transparent;

  .draft-lyrics-card__content {
    padding: 16px;
    display: flex;
    align-items: center;
    cursor: pointer;
  }

  .draft-lyrics-card__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: auto;
  }

  .draft-lyrics-card__title {
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }

  .draft-lyrics-card__text-right {
    margin-left: auto;
    text-align: right;
  }
}

.change-type-container {
  display: flex;
  align-items: center;
}

.change-type {
  text-transform: uppercase;
}

.change-type-icon {
  margin-right: 6px;
}

.changes {
  display: flex;

  .old-values {
    margin-right: 6px;
  }
}

.draft-lyrics-card__diff {
  width: 100%;
  border-top: 1px solid rgba(0,0,0,0.07);
}

.draft-lyrics-card--dark {
  .draft-lyrics-card__diff {
    border-top: 1px solid rgba(255,255,255,0.07);
  }
}
</style>
