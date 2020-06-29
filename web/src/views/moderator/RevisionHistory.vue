<template>
  <div>
    <h1>Revision History</h1>
    <revision-history-card />
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import RevisionHistoryCard from '@/components/moderator/RevisionHistoryCard.vue';

@Component({
  components: {
    RevisionHistoryCard,
  },
})
export default class RevisionHistory extends Vue {}
</script>

<style lang="scss" scoped>
.audit-card {
  height: 75px;
}
</style>
