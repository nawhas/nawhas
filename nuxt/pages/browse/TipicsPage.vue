<router>
path: /browse/topics
name: "topics.index"
</router>

<template>
  <div class="view-wrapper">
    <v-container class="app__section">
      <div class="section__title">
        <div>Featured Topics</div>
        <template v-if="featuredTopics">
          <v-row :dense="$vuetify.breakpoint.smAndDown">
            <v-col v-for="topic in featuredTopics" :key="topic.id" md="4" sm="6" cols="12">
              <topic-card :topic="topic" :featured="true" />
            </v-col>
          </v-row>
        </template>
        <template v-else>
          <skeleton-card-grid :limit="6" />
        </template>
      </div>
    </v-container>

    <v-container class="app__section">
      <div class="section__title">
        <div>All Topics</div>
      </div>

      <template v-if="topics">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="topic in topics" :key="topic.id" md="4" sm="6" cols="12">
            <topic-card :topic="topic" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid :limit="15" />
      </template>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { TopicIncludes, TopicsIndexResponse } from '@/api/topics';
import { generateMeta } from '@/utils/meta';
import { Topic } from '@/entities/topic';
import TopicCard from '@/components/topics/TopicCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';

interface Data {
  page: number;
  featuredTopics: Array<Topic> | null;
  topics: Array<Topic> | null;
  length: number;
}

export default Vue.extend({
  components: {
    TopicCard,
    SkeletonCardGrid,
  },

  async fetch() {
    // TODO - make this a popular topics route
    // const featuredTopics = await this.$api.topics.index({
    //   include: [TopicIncludes.related],
    //   pagination: { limit: 30, page: 1 },
    // });

    // TODO - include proper pagination once endpoint supports it
    const topics = await this.$api.topics.index({
      include: [TopicIncludes.Related],
      pagination: { limit: 30 },
    });

    // this.setTopics(featuredTopics, true);
    this.setTopics(topics, false);
  },

  data(): Data {
    const page = this.$route.query.page || 1;

    return {
      page: Number(page),
      length: 1,
      featuredTopics: null,
      topics: null,
    };
  },

  methods: {
    setTopics(data: TopicsIndexResponse, featured: boolean) {
      if (featured) {
        this.featuredTopics = data.data;
      } else {
        this.topics = data.data;

        // TODO - after enabling related and pagination, enable this control
        // this.length = data.meta.pagination.total_pages;
      }
    },
  },

  head: () => generateMeta({
    title: 'Topics',
  }),
});
</script>

<style lang="scss" scoped>
.view-wrapper {
  padding-top: 24px;
}
</style>
