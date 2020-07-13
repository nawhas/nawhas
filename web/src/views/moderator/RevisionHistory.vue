<template>
  <div>
    <h2>Revision History</h2>
    <revision-history-card v-for="item in items" :key="item.id" :audit="item" />
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import client from '@/services/client';
import RevisionHistoryCard from '@/components/moderator/RevisionHistoryCard.vue';
import { Data as AuditData, Audit } from '@/entities/audit';

@Component({
  components: {
    RevisionHistoryCard,
  },
})
export default class RevisionHistory extends Vue {
  private items: Array<AuditData> = [];

  created() {
    this.fetchAudit();
  }

  async fetchAudit() {
    const response = await client.get('v1/audit');
    this.items = response.data.data.map((data) => new Audit(data));
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
