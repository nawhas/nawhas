<router>
  path: /browse
  name: "browse.index"
</router>

<template>
  <div class="view-wrapper">
    <v-container class="app__section">
      <div class="section__title">
        <div>Top Reciters</div>
      </div>
      <template v-if="popularReciters">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="reciter in popularReciters" :key="reciter.id" md="4" sm="6" cols="12">
            <reciter-card featured v-bind="reciter" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid />
      </template>
      <v-row>
        <v-col class="text-center">
          <v-btn color="primary" to="/browse/reciters">
            View All
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-container class="app__section">
      <div class="section__title">
        <div>Popular Topics</div>
      </div>
      <template v-if="popularTopics">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="topic in popularTopics" :key="topic.id" md="4" sm="6" cols="12">
            <topic-card :topic="topic" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid />
      </template>
      <v-row>
        <v-col class="text-center">
          <v-btn color="primary" to="/browse/topics">
            View All
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import ReciterCard from '@/components/ReciterCard.vue';
import TopicCard from '@/components/topics/TopicCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import { ReciterIncludes } from '@/api/reciters';
import { Reciter } from '@/entities/reciter';
import { Topic } from '@/entities/topic';
import { generateMeta } from '@/utils/meta';
import { TopicIncludes } from '@/api/topics';

interface Data {
  popularReciters: Array<Reciter> | null;
  popularTopics: Array<Topic> | null;
}

export default Vue.extend({
  components: {
    ReciterCard,
    TopicCard,
    SkeletonCardGrid,
  },

  async fetch() {
    const [popularReciters, popularTopics] = await Promise.all([
      this.$api.reciters.popular({
        include: [ReciterIncludes.Related],
        pagination: { limit: 6 },
      }),
      // TODO - make this a popular topics route
      this.$api.topics.index({
        include: [TopicIncludes.Related],
        pagination: { limit: 6 },
      }),
    ]);

    this.popularReciters = popularReciters.data;
    this.popularTopics = popularTopics.data;
  },

  data: (): Data => ({
    popularReciters: null,
    popularTopics: null,
  }),

  head: () => generateMeta({
    title: 'Browse',
    // TODO - Add description
  }),
});
</script>

<style lang="scss" scoped>
.view-wrapper {
  padding-top: 24px;
}
.pagination {
  padding: 24px;
}
</style>
