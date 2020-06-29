<template>
  <div>
    <h1>Revision History</h1>
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
    this.items = [];
    const response = await client.get('v1/audit');
    this.items.push(new Audit(response.data));
  }
}
</script>

<style lang="scss" scoped>
.audit-card {
  height: 75px;
}
</style>
