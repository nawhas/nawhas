<template>
  <div :class="{ 'lyrics': true, 'lyrics--dark': isDark }">
    <div v-if="isJson">
      <div
        :class="{ 'lyrics__group': true, 'lyrics__group--highlighted': highlighter && highlighter.current === groupId }"
        v-for="(group, groupId) in lyrics.data"
        :key="groupId">
        <div class="lyrics__spacer" v-if="group.type === GroupType.SPACER"></div>
        <div class="lyrics__group__lines">
          <div class="lyrics__group__lines__line" v-for="line in group.lines" :key="line.text">
            <div class="lyrics__text">{{ line.text }}</div>
            <div class="lyrics__repeat" v-if="line.repeat">x{{ line.repeat }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="lyrics__plain-text" v-else v-html="lyrics"></div>
  </div>
</template>

<script lang="ts">
import {
  Component, Prop, Watch, Vue,
} from 'vue-property-decorator';
import * as Format from '@/constants/lyrics/format';
import * as GroupType from '@/constants/lyrics/group-type';
import LyricsHighlighter from '@/utils/LyricsHighlighter';
import { Lyrics, LyricsModel } from '@/types/lyrics';

@Component({
  data: () => ({ GroupType }),
})
export default class LyricsRenderer extends Vue {
  @Prop({ type: Object, required: true }) private readonly track!: any;
  private highlighter: LyricsHighlighter|null = null;

  get model(): LyricsModel|null {
    return (this.track.lyrics as LyricsModel|null);
  }

  get lyrics(): Lyrics|string {
    if (this.model === null) {
      return '';
    }

    if (this.isJson) {
      return JSON.parse(this.model.content);
    }

    return this.model.content.replace(/\n/gi, '<br>');
  }

  get isJson() {
    return this.model && this.model.format === Format.JSON_V1;
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

  @Watch('isCurrentlyPlaying')
  onPlayingTrackChanged(playing: boolean) {
    if (playing && this.hasTimestamps()) {
      this.highlighter = new LyricsHighlighter(this.$store.state.player, (this.lyrics as Lyrics));
    } else {
      this.highlighter = null;
    }
  }

  hasTimestamps(): boolean {
    if (!this.model) {
      return false;
    }

    if (this.model.format === Format.PLAIN_TEXT) {
      return false;
    }

    return (this.lyrics as Lyrics).meta.timestamps;
  }
}
</script>
