<template>
  <div :class="{ 'diff-table': true, 'diff-table--dark': $vuetify.theme.dark }">
    <div class="diff-table__actions">
      <v-btn-toggle mandatory dense v-model="view">
        <v-btn value="changed" x-small>
          Changed
        </v-btn>
        <v-btn value="all" x-small>
          All
        </v-btn>
      </v-btn-toggle>
    </div>
    <v-simple-table class="diff-table__table">
      <template #default>
        <thead>
          <tr>
            <th class="row__label"></th>
            <th class="text-left overline">Before</th>
            <th class="text-left overline">After</th>
          </tr>
        </thead>
        <tbody class="diff-table__body">
          <diff-table-row
              v-for="attr in attributes"
              :key="attr"
              :attribute="attr"
              :modified="modified[attr]"
              :original="original[attr]"
              :hide-unchanged="view === 'changed'"
          />
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import DiffTableRow from '@/components/moderator/DiffTableRow.vue';

@Component({
  components: { DiffTableRow },
})
export default class DiffTable extends Vue {
  @Prop({ type: Object, required: true }) private readonly original !: object;
  @Prop({ type: Object, required: true }) private readonly modified !: object;
  private view = 'changed';

  get attributes() {
    return Object.keys(this.modified);
  }
}
</script>

<style lang="scss" scoped>
.diff-table {
  width: 100%;
  display: block;
  overflow-x: auto;
  position: relative;
}
.diff-table__table {
  /*table-layout: fixed;*/
}
.diff-table__actions {
  padding: 10px;
  position: absolute;
  top: 0;
  right: 0;
}
.row__label {
  width: 120px;
}
</style>
