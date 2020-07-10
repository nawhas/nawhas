<template>
  <div :class="{ 'diff-viewer': true, 'diff-viewer--dark': $vuetify.theme.dark }">
    <pre
        v-for="(change, i) in diff"
        :key="i"
        :class="{
          segment: true,
          'segment--added': change.added,
          'segment--removed': change.removed,
        }"
        v-html="change.value"
    ></pre>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import * as Diff from 'diff';

@Component
export default class DiffViewer extends Vue {
  @Prop({ type: String, required: true }) private readonly original !: string;
  @Prop({ type: String, required: true }) private readonly modified !: string;

  get diff() {
    return Diff.diffWords(this.original, this.modified);
  }
}
</script>

<style lang="scss" scoped>
@import '../../styles/theme';

.diff-viewer {
  width: 100%;
  display: block;
  padding: 12px;
}
.segment {
  display: inline;
  white-space: pre-wrap;
}
.segment--removed {
  background-color: map-deep-get($colors, 'red', 'lighten-4');
}
.segment--added {
  background-color: map-deep-get($colors, 'green', 'lighten-4');
}

.diff-viewer--dark {
  .segment--removed {
    background-color: darken(map-deep-get($colors, 'red', 'darken-3'), 10%);
  }
  .segment--added {
    background-color: darken(map-deep-get($colors, 'green', 'darken-3'), 10%);
  }
}
</style>
