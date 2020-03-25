<template>
  <div>
    <div v-if="isJson">
      <template v-for="(group, groupId) in lyrics">
        <div
          :class="{'lyrics__group': true, 'lyrics_active': isCurrentLyric(group, groupId)}"
          :key="groupId"
          :ref="`group-${groupId}`"
        >
          <div v-if="!mobile" class="lyrics__group__timestamp">{{ formattedTimestamp(group.timestamp) }}</div>
          <div class="lyrics__group__lines">
            <span class="lyrics__group__lines__line" v-for="line in group.lines" :key="line.text">
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
  @Prop()
  isCurrentTrack!: boolean;

  get seek() {
    return this.$store.state.player.seek;
  }

  get formattedSeek() {
    return moment
      .utc(moment.duration(this.seek, 'seconds').asMilliseconds())
      .format('mm:ss');
  }

  get isJson() {
    try {
      JSON.parse(this.lyricObject);
    } catch (e) {
      return false;
    }
    return true;
  }

  get lyrics() {
    return (this.isJson) ? JSON.parse(this.lyricObject) : this.lyricObject.replace(/\n/gi, '<br>');
  }

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

  formattedTimestamp(timestamp) {
    return moment
      .utc(moment.duration(timestamp, 'seconds').asMilliseconds())
      .format('mm:ss');
  }

  isCurrentLyric(group, groupId) {
    // If the track that is playing is not the same
    // to the one that is being displayed
    // Do not highlight anything
    if (!this.isCurrentTrack) {
      return false;
    }

    // If the timestamp is greater than the audio player seek
    // return false
    if (group.timestamp > this.seek) {
      return false;
    }
    const nextGroup = this.lyrics[groupId + 1];
    // If there is no more lines available
    // We have readhed the end of the track
    // So return true
    if (nextGroup === undefined) {
      return true;
    }
    // If the next group timestamp is less than the audio player seek
    // return false
    if (nextGroup.timestamp < this.seek) {
      return false;
    }
    const ref = `group-${groupId}`;
    this.$nextTick(() => this.$refs[ref][0].scrollIntoView({ block: 'center', behavior: 'smooth' }));
    return true;
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

.lyrics_active {
  // background-color: deeppink;
  color: white;
  font-weight: bolder;
  padding: 10px 10px 10px 0px;
}

.lyrics__repeat {
  margin-left: 10px;
  background-color: #c4c4c4;
  padding: 3px 6px;
  border-radius: 8px;
  color: black;
}
</style>
