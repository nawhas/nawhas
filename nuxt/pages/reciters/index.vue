<template>
  <div class="view-wrapper">
    <v-container class="app__section">
      <h5 class="section__title">
        Top Reciters
      </h5>
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
    </v-container>

    <v-container class="app__section">
      <div class="section__title section__title--with-actions">
        <div>All Reciters</div>
        <v-btn icon @click="focusSearch">
          <v-icon>search</v-icon>
        </v-btn>
      </div>
      <template v-if="reciters">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="reciter in reciters" :key="reciter.id" md="4" sm="6" cols="12">
            <reciter-card v-bind="reciter" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid :limit="15" />
      </template>
      <div class="pagination">
        <v-pagination
          v-model="page"
          color="deep-orange"
          :length="length"
          circle
          next-icon="navigate_next"
          prev-icon="navigate_before"
          @input="onPageChanged"
        />
      </div>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import ReciterCard from '@/components/ReciterCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import { EventBus, Search } from '@/events';
import { ReciterIncludes, RecitersIndexResponse } from '@/api/reciters';
import { Reciter } from '@/entities/reciter';

interface Data {
  page: number;
  reciters: Array<Reciter> | null;
  popularReciters: Array<any> | null;
  length: number;
}

export default Vue.extend({
  components: {
    ReciterCard,
    SkeletonCardGrid,
  },

  async fetch() {
    const [reciters, popular] = await Promise.all([
      this.$api.reciters.index({
        include: [ReciterIncludes.Related],
        pagination: { limit: 30, page: this.page },
      }),
      this.$api.reciters.popular({
        include: [ReciterIncludes.Related],
        pagination: { limit: 6 },
      }),
    ]);

    this.setReciters(reciters);
    this.popularReciters = popular.data;
  },

  data (): Data {
    const page = this.$route.query.page || 1;

    return {
      page: Number(page),
      length: 1,
      reciters: null,
      popularReciters: null,
    };
  },

  watch: {
    '$route.query': '$fetch',
  },

  methods: {
    setReciters(data: RecitersIndexResponse) {
      this.reciters = data.data;
      this.length = data.meta.pagination.total_pages;
    },

    focusSearch() {
      this.$nextTick(() => EventBus.$emit(Search.TRIGGER));
    },

    onPageChanged(page) {
      this.$router.push({ query: { page: String(page) } });
    },
  },

  head: {
    title: 'Reciters',
  },
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
