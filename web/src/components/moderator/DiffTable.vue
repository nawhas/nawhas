<template>
  <div :class="{ 'diff-table': true, 'diff-table--dark': $vuetify.theme.dark }">
    <v-simple-table>
      <template #default>
        <thead>
          <tr>
            <th></th>
            <th class="text-left overline">Before</th>
            <th class="text-left overline">After</th>
          </tr>
        </thead>
        <tbody class="diff-table__body">
          <tr v-for="attr in attributes" :key="attr" :class="rowClasses(attr)">
            <td class="overline">{{ attr | startCase }}</td>
            <td>{{ original[attr] || 'N/A' }}</td>
            <td>{{ modified[attr] || 'N/A' }}</td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component
export default class DiffTable extends Vue {
  @Prop({ type: Object, required: true }) private readonly original !: object;
  @Prop({ type: Object, required: true }) private readonly modified !: object;

  get attributes() {
    return Object.keys(this.modified);
  }

  rowClasses(attr: string) {
    return {
      'diff-table__row': true,
      'diff-table__row--modified': this.original[attr] !== this.modified[attr],
    };
  }
}
</script>

<style lang="scss" scoped>
@import '../../styles/theme';

.diff-table {
  width: 100%;
  display: block;
  table-layout: fixed;
}
.diff-table__row {
  td, th {
    padding-top: 12px;
    padding-bottom: 8px;
  }
}
.diff-table__row--modified {
  background-color: map-deep-get($colors, 'amber', 'lighten-5');
}

.diff-table--dark {
  .diff-table__row--modified {
    background-color: change-color(map-deep-get($colors, 'blue', 'base'), $alpha: 0.05);
  }
}
</style>
