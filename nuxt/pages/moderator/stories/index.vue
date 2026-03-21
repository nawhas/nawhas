<router>
  path: /moderator/stories
  name: "moderator.stories.index"
</router>

<template>
  <v-container class="app__section mt-4">
    <div class="stories-moderator__header">
      <h2 dusk="moderator-stories__heading">
        Stories
      </h2>
      <v-btn color="primary" :to="{ name: 'moderator.stories.new' }">
        New story
      </v-btn>
    </div>

    <div v-if="stories.length > 0" class="stories-moderator__list">
      <v-overlay v-if="$fetchState.pending" absolute class="stories-moderator__loading">
        <v-progress-circular indeterminate />
      </v-overlay>
      <v-simple-table>
        <thead>
          <tr>
            <th class="text-left">
              Title
            </th>
            <th class="text-left">
              Slug
            </th>
            <th class="text-left">
              Status
            </th>
            <th class="text-right">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="story in stories" :key="story.id">
            <td>{{ story.title }}</td>
            <td>
              <code>{{ story.slug }}</code>
            </td>
            <td>
              <v-chip v-if="story.publishedAt" small color="success" text-color="white">
                Published
              </v-chip>
              <v-chip v-else small color="grey" text-color="white">
                Draft
              </v-chip>
            </td>
            <td class="text-right">
              <v-btn
                icon
                :to="{ name: 'moderator.stories.edit', params: { id: story.id } }"
                aria-label="Edit story"
              >
                <v-icon>edit</v-icon>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-simple-table>
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
    <div v-else-if="$fetchState.pending" class="stories-moderator__loading text-center">
      <v-progress-circular indeterminate />
    </div>
    <div v-else class="stories-moderator__empty">
      No stories yet. Create one to get started.
    </div>
  </v-container>
</template>

<script lang="ts">
import Vue from 'vue';
import { Story } from '@/entities/story';
import { getPage } from '@/utils/route';

interface Data {
  stories: Array<Story>;
  page: number;
  length: number;
}

export default Vue.extend({
  layout: 'moderator',
  data(): Data {
    return {
      stories: [],
      page: getPage(this.$route),
      length: 0,
    };
  },
  head: {
    title: 'Stories',
  },
  watch: {
    '$route.query': '$fetch',
  },
  async fetch() {
    const response = await this.$api.stories.index({
      pagination: { limit: 30, page: this.page },
    });
    this.stories = response.data;
    this.length = response.meta.pagination.total_pages;
  },
  methods: {
    onPageChanged(page: number) {
      this.$vuetify.goTo(0);
      this.$router.push({ query: { page: String(page) } });
    },
  },
});
</script>

<style scoped lang="scss">
.stories-moderator__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}
h2 {
  font-weight: 300;
  font-size: 34px;
  margin: 0;
}
.stories-moderator__empty {
  text-align: center;
  padding: 24px 0;
  font-size: 24px;
  font-weight: 200;
  opacity: 0.7;
}
.stories-moderator__list {
  position: relative;
}
.stories-moderator__loading {
  padding-top: 24px;
  align-items: flex-start;
}
</style>
