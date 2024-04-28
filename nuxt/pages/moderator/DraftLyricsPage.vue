<router>
  path: /moderator/drafts/lyrics
  name: "moderator.drafts.lyrics"
</router>

<template>
  <v-container class="app__section mt-4">
    <h2>Draft Lyrics</h2>
    <div v-if="draftLyrics.length > 0" class="revisions">
      <v-overlay v-if="$fetchState.pending" absolute class="revisions__loading">
        <v-progress-circular
          indeterminate
        />
      </v-overlay>
      <div
        v-for="draft in draftLyrics"
        :key="draft.id"
      >
        {{ draft.trackId }}
      </div>
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
import { DraftLyrics } from '@/entities/lyrics';
import { getPage } from '@/utils/route';

interface Data {
  draftLyrics: Array<DraftLyrics>;
  page: number;
  length: number;
}

export default Vue.extend({
  layout: 'moderator',
  data(): Data {
    return {
      draftLyrics: [],
      page: getPage(this.$route),
      length: 0,
    };
  },
  head: {
    title: 'Draft Lyrics',
  },
  watch: {
    '$route.query': '$fetch',
  },
  async fetch() {
    const response = await this.$api.draftLyrics.getAllDraftLyrics({
      pagination: { limit: 30, page: this.page },
    });
    this.draftLyrics = response.data;
    this.length = response.meta.pagination.total_pages;
  },
  methods: {
    onPageChanged(page) {
      this.$vuetify.goTo(0);
      this.$router.push({ query: { page: String(page) } });
    },
  },
});
</script>
