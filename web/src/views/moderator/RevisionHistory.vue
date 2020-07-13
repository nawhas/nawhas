<template>
  <div>
    <h2>Revision History</h2>
    <revision-history-card
        v-for="revision in revisions"
        :key="revision.id"
        :revision="revision" />
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

  created() {
    this.fetch();
  }

  async fetch() {
    const response = await client.get('v1/revisions');
    this.revisions = response.data.data.map((data) => new Revision(data));
  }
}
</script>

<style lang="scss" scoped>
h2 {
  font-weight: 300;
  font-size: 34px;
  margin-bottom: 16px;
}
</style>
