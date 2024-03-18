<router>
path: /browse
name: "browse.index"
</router>

<template>
  <div>
    <browse-by-card
      browse-by="reciter"
      :popular-reciters="popularReciters"
    />
    <browse-by-card
      browse-by="topic"
      :topics="popularTopics"
    />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import BrowseByCard from '@/components/BrowseByCard.vue';
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
    BrowseByCard,
  },

  async fetch() {
    const [popularReciters, popularTopics] = await Promise.all([
      this.$api.reciters.popular({
        include: [ReciterIncludes.Related],
        pagination: { limit: 6 },
      }),
      this.$api.topics.popular({
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
</style>
