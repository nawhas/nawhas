<template>
  <div>
    <h2>Revision History</h2>
    <div class="revisions" v-if="revisions.length > 0">
      <revision-history-card
          v-for="revision in revisions"
          :key="revision.id"
          :revision="revision" />
    </div>
    <div class="revisions__loading text-center" v-else-if="loading">
      <v-progress-circular indeterminate />
    </div>
    <div class="revisions__empty" v-else>
      There's nothing here.
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import client from '@/services/client';
import RevisionHistoryCard from '@/components/moderator/RevisionHistoryCard.vue';
import { Revision } from '@/entities/revision';

@Component({
  components: {
    RevisionHistoryCard,
  },
})
export default class RevisionHistory extends Vue {
  private revisions: Array<Revision> = [];
  private loading = true;

  created() {
    this.fetch();
  }

  async fetch() {
    this.loading = true;
    const response = await client.get('v1/revisions');
    this.revisions = response.data.data.map((data) => new Revision(data));
    this.loading = false;
  }
}
</script>

<style lang="scss" scoped>
h2 {
  font-weight: 300;
  font-size: 34px;
  margin-bottom: 16px;
}
.revisions__empty {
  text-align: center;
  padding: 24px 0;
  font-size: 24px;
  font-weight: 200;
  opacity: 0.7;
}
</style>
