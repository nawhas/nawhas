<template>
  <tr v-show="!hideUnchanged || changed" :class="classes">
    <td class="row__label overline">
      {{ attribute | startCase }}
    </td>
    <td v-if="changed">
      <span v-if="old && isExternalLink" class="segment">
        <a class="external-link" :href="old" target="_blank" :title="old">Open URL</a>
      </span>
      <span
        v-else-if="old && isArray"
        class="segment"
        v-text="`${old.length} items`"
      />
      <span v-else-if="old">
        <span
          v-for="(change, i) in left"
          :key="i"
          :class="{
            segment: true,
            'segment--removed': change.removed,
          }"
          v-text="change.value"
        />
      </span>
      <span v-else class="segment text--disabled">NULL</span>
    </td>
    <td :colspan="changed ? 1 : 2">
      <span v-if="isExternalLink" class="segment">
        <a class="external-link" :href="value" target="_blank" :title="value">Open URL</a>
      </span>
      <span v-else-if="isArray" class="segment" v-text="`${value.length} items`" />
      <span v-else-if="changed">
        <span
          v-for="(change, i) in right"
          :key="i"
          :class="{
            segment: true,
            'segment--added': change.added,
          }"
          v-text="change.value"
        />
      </span>
      <span v-else class="segment" v-text="value" />
    </td>
  </tr>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import * as Diff from 'diff';

const externalUrlAttributes = [
  'avatar', 'artwork', 'audio',
];

@Component
export default class DiffTableRow extends Vue {
  @Prop({ type: String, required: true }) private readonly attribute !: string;
  @Prop() private readonly old !: unknown|undefined;
  @Prop() private readonly value !: unknown;
  @Prop({ type: Boolean, default: false }) private readonly hideUnchanged !: string;

  get classes() {
    return {
      'diff-table__row': true,
      'diff-table__row--dark-mode': this.$vuetify.theme.dark,
      'diff-table__row--modified': this.old !== undefined,
    };
  }

  get diff() {
    const old: string = String(this.old ?? '');
    const snapshot: string = String(this.value ?? '');

    return Diff.diffChars(old, snapshot);
  }

  get changed() {
    return this.old !== undefined;
  }

  get left() {
    return this.diff.filter((change) => !change.added);
  }

  get right() {
    return this.diff.filter((change) => !change.removed);
  }

  get isArray(): boolean {
    return Array.isArray(this.value);
  }

  get isExternalLink() {
    return externalUrlAttributes.includes(this.attribute);
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.diff-table__row {
  td {
    padding-top: 12px;
    padding-bottom: 8px;
  }
  td.row__label {
    width: 150px;
    white-space: nowrap;
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
