<template>
  <div :class="{ 'lyrics': true, 'lyrics--dark': isDark }">
    <div v-if="!track.lyrics" class="lyrics__empty lyrics__empty--unavailable">
      <div class="lyrics__empty-message">
        We don't have a write-up for this nawha yet.
      </div>
      <div class="lyrics__empty-actions">
        <edit-draft-lyrics :track="track" cta="Add a Write Up" />
      </div>
    </div>
    <div v-else-if="unsupported" class="lyrics__empty lyrics__empty--unsupported">
      <div class="lyrics__empty-message">
        An update is required to view this write-up.
      </div>
    </div>
    <div v-else-if="isJson">
      <div
        v-for="(group, groupId) in lyrics.data"
        :key="groupId"
        :class="{ 'lyrics__group': true, 'lyrics__group--highlighted': highlighter && highlighter.current === groupId }"
      >
        <div v-if="group.type === GroupType.Spacer" class="lyrics__spacer" />
        <div class="lyrics__group__lines">
          <div v-for="(line, lineId) in group.lines" :key="lineId" class="lyrics__group__lines__line">
            <div class="lyrics__text">
              {{ line.text }}
            </div>
            <div v-if="line.repeat" class="lyrics__repeat">
              x{{ line.repeat }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="lyrics__plain-text" v-html="lyrics" />
  </div>
</template>

<script lang="ts">
import {
  Component, Prop, Watch, Vue,
} from 'nuxt-property-decorator';
import { Track } from '@/entities/track';
import { Lyrics, Format, Documents } from '@/entities/lyrics';
import LyricsHighlighter from '@/utils/LyricsHighlighter';
import JsonV1Document = Documents.JsonV1.Document;
import GroupType = Documents.JsonV1.LineGroupType;

@Component({
  data: () => ({ GroupType }),
})
export default class LyricsRenderer extends Vue {
  @Prop({ type: Object, required: true }) private readonly track!: Track;
  private highlighter: LyricsHighlighter|null = null;

  get model(): Lyrics|null {
    return this.track.lyrics ?? null;
  }

  get unsupported(): boolean {
    if (!this.track) {
      return false;
    }
    return this.track.lyrics ? this.track.lyrics.format > Format.JsonV1 : false;
  }

  get lyrics(): JsonV1Document|string {
    if (this.model === null) {
      return '';
    }

    if (this.isJson) {
      return JSON.parse(this.model.content);
    }

    return this.model.content.replace(/\n/gi, '<br>');
  }

  get isJson() {
    return this.model && this.model.format === Format.JsonV1;
  }

  get isCurrentlyPlaying() {
    const playing = this.$store.getters['player/track'];

    if (!playing) {
      return false;
    }

    return this.track.id === playing.track.id;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  get highlightedGroup() {
    return this.highlighter ? this.highlighter.current : null;
  }

  mounted() {
    this.setUpHighlighter(this.isCurrentlyPlaying);
  }

  @Watch('isCurrentlyPlaying')
  setUpHighlighter(playing: boolean) {
    if (playing && this.hasTimestamps()) {
      this.highlighter = new LyricsHighlighter(this.$store.state.player, (this.lyrics as JsonV1Document));
    } else {
      this.highlighter = null;
    }
  }

  @Watch('highlightedGroup')
  onHighlightedGroupChanged(value) {
    this.$emit('highlight:changed', value);
  }

  hasTimestamps(): boolean {
    if (!this.model) {
      return false;
    }

    if (this.model.format === Format.PlainText) {
      return false;
    }

    return (this.lyrics as JsonV1Document).meta.timestamps;
  }
}
</script>
