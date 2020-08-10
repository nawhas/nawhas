<template>
  <lyrics-renderer
    v-if="track.lyrics"
    ref="lyrics"
    class="renderer"
    :track="track"
    @highlight:changed="scrollToCurrentLyricsGroup"
  />
</template>

<script lang="ts">
import {
  Component, Prop, Ref, Vue, Watch,
} from 'nuxt-property-decorator';
import * as NoSleep from 'nosleep.js/dist/NoSleep';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';

@Component({
  components: {
    LyricsRenderer,
  },
})
export default class LyricsOverlay extends Vue {
  @Prop({ type: Object }) private readonly track;
  @Ref('lyrics') private readonly renderer!: Vue;
  private lock = new NoSleep();

  mounted() {
    this.lock.enable();
  }

  @Watch('track')
  onTrackChanged() {
    this.renderer.$el.scrollTo({
      behavior: 'smooth',
      top: 0,
      left: 0,
    });
  }

  scrollToCurrentLyricsGroup(id) {
    if (id === null) {
      return;
    }

    this.$nextTick(() => {
      const highlighted = this.renderer.$el.querySelector('.lyrics__group--highlighted');
      if (highlighted) {
        highlighted.scrollIntoView({ block: 'center', behavior: 'smooth' });
      }
    });
  }

  beforeDestroy() {
    this.lock.disable();
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

$inactive-color: rgba(255, 255, 255, 0.3);
$active-color: rgba(255, 255, 255, 1);

.renderer {
  padding: 0 36px;
  overflow-y: auto;
  overflow-x: hidden;
  white-space: normal;
  text-align: left;
  height: 100%;
  width: 100%;
}

.renderer ::v-deep .lyrics__plain-text {
  font-size: 18px;
}

.renderer ::v-deep .lyrics__group {
  padding: 8px 0;
  color: $inactive-color;
  font-size: 28px;
  font-weight: 600;
  @include transition(color, font-weight);

  .lyrics__spacer {
    display: none;
  }

  .lyrics__text {
    display: inline;
  }

  .lyrics__repeat {
    display: inline-block;
    margin-left: 8px;
    margin-bottom: 3px;
    padding: 5px 8px;
    text-align: center;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Roboto Mono', monospace;
    font-weight: 600;
    line-height: 14px;
    border: 1px solid $inactive-color;
    vertical-align: middle;
    @include transition(border-color);
  }

  &.lyrics__group--highlighted {
    color: white;
    font-weight: 700;

    .lyrics__repeat {
      border-color: rgba(255,255,255,0.76);
    }
  }
}
</style>
