<router>
  path: /moderator/revisions
  name: "moderator.revisions"
</router>

<template>
  <v-container class="app__section mt-4">
    <h2>Revision History</h2>
    <div v-if="revisions.length > 0" class="revisions">
      <v-overlay v-if="$fetchState.pending" absolute class="revisions__loading">
        <v-progress-circular
          indeterminate
        />
      </v-overlay>
      <revision-history-card
        v-for="revision in revisions"
        :key="revision.id"
        :revision="revision"
      />
      <v-pagination
        v-model="page"
        class="my-8"
        color="deep-orange"
        :length="length"
        :total-visible="10"
        circle
        next-icon="navigate_next"
        prev-icon="navigate_before"
        @input="onPageChanged"
      />
    </div>
    <div v-else-if="$fetchState.pending" class="revisions__loading text-center">
      <v-progress-circular indeterminate />
    </div>
    <div v-else class="revisions__empty">
      There's nothing here.
    </div>
  </v-container>
</template>

<script lang="ts">
import Vue from 'vue';
import RevisionHistoryCard from '@/components/moderator/revisions/RevisionHistoryCard.vue';
import { Revision } from '@/entities/revision';
import { getPage } from '@/utils/route';

interface Data {
  revisions: Array<Revision>;
  page: number;
  length: number;
}

export default Vue.extend({

  components: {
    RevisionHistoryCard,
  },
  layout: 'moderator',

  data(): Data {
    return {
      revisions: [],
      page: getPage(this.$route),
      length: 0,
    };
  },

  async fetch() {
    const response = await this.$api.revisions.index({
      pagination: { limit: 30, page: this.page },
    });
    this.revisions = response.data;
    this.length = response.meta.pagination.total_pages;
  },

  head: {
    title: 'Revisions',
  },

  watch: {
    '$route.query': '$fetch',
  },

  methods: {
    onPageChanged(page) {
      this.$vuetify.goTo(0);
      this.$router.push({ query: { page: String(page) } });
    },
  },
});
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
.revisions {
  position: relative;
}
.revisions__loading {
  padding-top: 24px;
  align-items: flex-start;
}
</style>
