<template>
  <div>
    <div v-if="isJson">
      <template v-for="(lyric, index) in lyrics">
        <div class="lyrics__group" :key="index">
          <div class="lyrics__group__timestamp">{{ lyric.timestamp }}</div>
          <div class="lyrics__group__lines">
            <span class="lyrics__group__lines__line" v-for="line in lyric.lines" :key="line.text">
              <span>{{ line.text }}</span>
              <span class="lyrics__repeat" v-if="line.repeat">x{{ line.repeat }}</span>
            </span>
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
import * as moment from 'moment';

@Component
export default class LyircsPage extends Vue {
  @Prop({ type: String }) private lyricObject: any;

  get seek() {
    return this.$store.state.player.seek;
  }

  get formattedSeek() {
    return moment
      .utc(moment.duration(this.seek, 'seconds').asMilliseconds())
      .format('mm:ss');
  }

  get isJson() {
    return !!(this.lyricObject.startsWith('['));
  }

  get lyrics() {
    return (this.isJson) ? JSON.parse(this.lyricObject) : this.lyricObject.replace(/\n/gi, '<br>');
  }

  isCurrentLyric(lyric, index) {
    if (lyric.timestamp < this.formattedSeek) {
      const nextLyric = this.lyrics[index + 1]
        .timestamp;
      if (lyric.timestamp < nextLyric) {
        if (nextLyric < this.formattedSeek) {
          return false;
        }
        return true;
      }
    }
    return false;
  }
}
</script>

<style lang="scss" scoped>
.lyrics__group {
  display: flex;
  // margin-bottom: 15px;

  .lyrics__group__timestamp {
    color: #a6a6a6;
    margin-right: 16px;
  }

  .lyrics__group__lines {
    .lyrics__group__lines__line {
      display: block;
    }
  }
}

.lyrics__repeat {
  margin-left: 10px;
  background-color: #c4c4c4;
  padding: 3px 6px;
  border-radius: 8px;
  color: black;
}
</style>
