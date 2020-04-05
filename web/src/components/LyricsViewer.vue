<template>
  <div :class="{ 'lyrics': true, 'lyrics--dark': isDark }">
    <div v-if="isJson">
      <template v-for="(group, groupId) in lyrics.data">
        <div
          :class="{'lyrics__group': true, 'lyrics__group--highlighted': highlighter && highlighter.current === groupId}"
          :key="groupId"
          :ref="`group-${groupId}`"
        >
          <div class="lyrics__spacer" v-if="group.type === GroupType.SPACER"></div>
          <div class="lyrics__group__lines">
            <div class="lyrics__group__lines__line" v-for="line in group.lines" :key="line.text">
              <div class="lyrics__text">{{ line.text }}</div>
              <div class="lyrics__repeat" v-if="line.repeat">x{{ line.repeat }}</div>
            </div>
          </div>
        </div>
      </template>
    </div>
    <div v-else v-html="lyrics"></div>
  </div>
</template>

<script lang="ts">
import {
  Component, Prop, Vue,
} from 'vue-property-decorator';
import * as Format from '@/constants/lyrics/format';
import * as GroupType from '@/constants/lyrics/group-type';
import LyricsHighlighter from '@/utils/LyricsHighlighter';
import { Lyrics, LyricsModel } from '@/types/lyrics';

@Component({
  data: () => ({ GroupType }),
})
export default class LyricsViewer extends Vue {
  @Prop({ type: Object, required: true }) private readonly model!: LyricsModel;
  @Prop() private readonly current!: boolean;
  private highlighter: LyricsHighlighter|null = null;

  mounted() {
    if (this.hasTimestamps) {
      this.highlighter = new LyricsHighlighter(this.$store.state.player, (this.lyrics as Lyrics));
    }
  }

  get lyrics(): Lyrics|string {
    if (this.isJson) {
      return JSON.parse(this.model.content);
    }
    return this.model.content.replace(/\n/gi, '<br>');
  }

  get seek() {
    return this.$store.state.player.seek;
  }

  get hasTimestamps() {
    if (this.model.format === Format.PLAIN_TEXT) {
      return false;
    }

    return (this.lyrics as Lyrics).meta.timestamps;
  }

  get isJson() {
    return this.model.format === Format.JSON_V1;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }
}
</script>

<style lang="scss" scoped>
@import '../styles/theme';

.lyrics {
  padding: 24px 0;
  font-weight: 400;
  font-size: 1.1rem;
  line-height: 2.3rem;
}

.lyrics__group {
  display: flex;
  border-left: 3px solid transparent;
  padding: 3px 24px;
  color: rgba(0, 0, 0, 0.76);

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
}

.lyrics__group--highlighted {
  border-left: 3px solid $primary;
  background-color: rgba(0, 0, 0, 0.07);
  color: black;
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

.lyrics--dark {
  .lyrics__group {
    color: rgba(255, 255, 255, 0.76);
  }
  .lyrics__group__timestamp {
     color: #a6a6a6;
  }
  .lyrics__repeat {
    border-color: rgba(255,255,255,0.76);
  }
  .lyrics__group--highlighted {
    color: white;
    border-left: 3px solid $primary;
    background-color: rgba(255, 255, 255, 0.07);
  }
}
</style>
