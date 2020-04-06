<template>
  <div :class="{ 'lyrics-viewer': true }">
    <lyrics-renderer class="lyrics-content" :track="track" />
  </div>
</template>

<script lang="ts">
import {
  Component, Prop, Vue,
} from 'vue-property-decorator';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';

@Component({
  components: {
    LyricsRenderer,
  },
})
export default class LyricsViewer extends Vue {
  @Prop({ type: Object, required: true }) private readonly track!: any;
}
</script>

<style lang="scss" scoped>
@import '../styles/theme';

.lyrics-viewer {
  padding: 24px 0;
  font-weight: 400;
  font-size: 1.1rem;
  line-height: 2.3rem;
}
.lyrics-content ::v-deep .lyrics__plain-text {
  padding: 0 24px;
}
.lyrics-content ::v-deep .lyrics__group {
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

.lyrics-content.lyrics--dark {
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
