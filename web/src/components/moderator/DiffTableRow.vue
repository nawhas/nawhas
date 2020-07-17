<template>
  <tr :class="classes" v-show="!hideUnchanged || original !== modified">
    <td class="row__label overline">{{ attribute | startCase }}</td>
    <td>
      <span v-if="original && isImage">
        <a class="external-link" :href="original" target="_blank">View Image</a>
      </span>
      <span v-else-if="original">
        <span
            v-for="(change, i) in left"
            :key="i"
            :class="{
              segment: true,
              'segment--removed': change.removed,
            }"
            v-text="change.value"
        ></span>
      </span>
      <span v-else class="text--disabled">N/A</span>
    </td>
    <td>
      <span v-if="modified && isImage">
        <a class="external-link" :href="modified" target="_blank">View Image</a>
      </span>
      <span v-else-if="modified">
        <span
            v-for="(change, i) in right"
            :key="i"
            :class="{
              segment: true,
              'segment--added': change.added,
            }"
            v-text="change.value"
        ></span>
      </span>
      <span v-else class="text--disabled">N/A</span>
    </td>
  </tr>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import * as Diff from 'diff';

const imageAttributes = [
  'avatar', 'artwork',
];

@Component
export default class DiffTableRow extends Vue {
  @Prop({ type: String, required: true }) private readonly attribute !: string;
  @Prop() private readonly original !: string;
  @Prop() private readonly modified !: string;
  @Prop({ type: Boolean, default: false }) private readonly hideUnchanged !: string;

  get classes() {
    return {
      'diff-table__row': true,
      'diff-table__row--dark-mode': this.$vuetify.theme.dark,
      'diff-table__row--modified': this.original !== this.modified,
    };
  }

  get diff() {
    const original = this.original || '';
    const modified = this.modified || '';

    return Diff.diffChars(String(original), String(modified));
  }

  get left() {
    return this.diff.filter((change) => !change.added);
  }

  get right() {
    return this.diff.filter((change) => !change.removed);
  }

  get isImage() {
    return imageAttributes.includes(this.attribute);
  }
}
</script>

<style lang="scss" scoped>
@import '../../styles/theme';

.diff-table__row {
  td {
    padding-top: 12px;
    padding-bottom: 8px;
    white-space: pre-wrap;
  }
  td.row__label {
    vertical-align: top;
    padding-top: 16px;
    width: 120px;
  }
}
.diff-table__row--modified, .diff-table__row--modified:hover {
  background-color: map-deep-get($colors, 'amber', 'lighten-5') !important;
}

.diff-table__row--dark-mode {
  &.diff-table__row--modified, &.diff-table__row--modified:hover {
    background-color: change-color(map-deep-get($colors, 'blue', 'base'), $alpha: 0.05) !important;
  }
}

tr:not(.diff-table__row--modified):hover {
  background-color: transparent !important;
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

.diff-table__row--dark-mode {
  .segment--removed {
    background-color: darken(map-deep-get($colors, 'red', 'darken-3'), 10%);
  }
  .segment--added {
    background-color: darken(map-deep-get($colors, 'green', 'darken-3'), 10%);
  }
}

.external-link {
  text-decoration: none;
}
</style>
