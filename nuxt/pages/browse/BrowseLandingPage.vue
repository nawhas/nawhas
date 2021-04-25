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
      :topics="{}"
    />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { ReciterIncludes } from '@/api/reciters';
import { generateMeta } from '@/utils/meta';
import BrowseByCard from '@/components/BrowseByCard.vue';

interface Data {
  popularReciters: Array<any> | null;
  topics: Array<any> | null;
}

export default Vue.extend({
  components: {
    BrowseByCard,
  },

  async fetch() {
    const [popular] = await Promise.all([
      this.$api.reciters.popular({
        include: [ReciterIncludes.Related],
        pagination: { limit: 6 },
      }),
    ]);

    this.popularReciters = popular.data;
  },

  data(): Data {
    return {
      popularReciters: null,
      topics: null,
    };
  },

  methods: {
  },
  head: () => generateMeta({
    title: 'Browse',
    description: 'Browse, read, and listen to over 6000 nawhas by more than 100 different reciters, ' +
      'including world-famous reciters like Nadeem Sarwar, Irfan Haider, Tejani Brothers, ' +
      'Hassan Sadiq, Mir Hasan Mir, and more!',
  }),
});
</script>

<style lang="scss" scoped>

</style>
