<template>
  <div>
    <h2>Revision History</h2>
    <div class="revisions" v-if="revisions.length > 0">
      <revision-history-card
          v-for="revision in revisions"
          :key="revision.id"
          :revision="revision" />
      <v-pagination color="deep-orange" v-model="page" :length="length" circle @input="goToPage"></v-pagination>
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
  private page = 1;
  private length = 0;

  created() {
    this.fetch(this.page);
  }

  async fetch(page) {
    this.loading = true;
    this.revisions = [];
    const response = await client.get('v1/revisions', {
      page,
    });
    this.revisions = response.data.data.map((data) => new Revision(data));
    this.length = response.data.meta.pagination.total_pages;
    this.loading = false;
  }

  goToPage(number) {
    this.fetch(number);
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
