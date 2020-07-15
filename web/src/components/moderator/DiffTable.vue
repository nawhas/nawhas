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
          <diff-table-row
              v-for="attr in attributes"
              :key="attr"
              :attribute="attr"
              :modified="modified[attr]"
              :original="original[attr]"
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

  get attributes() {
    return Object.keys(this.modified);
  }
}
</script>

<style lang="scss" scoped>
.diff-table {
  width: 100%;
  display: block;
  table-layout: fixed;
  overflow-x: auto;
}
</style>
