<template>
  <div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="!loading">
          <v-layout row wrap>
            <v-flex xs12 sm6 md4 v-for="reciter in popularReciters" :key="reciter.id">
              <reciter-card featured v-bind="reciter" />
            </v-flex>
          </v-layout>
        </template>
        <template v-else>
          <six-card-skeleton />
        </template>
      </v-container>
    </section>

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">All Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="!loading">
          <v-layout row wrap>
            <v-flex xs12 sm6 md4 v-for="reciter in reciters" :key="reciter.id">
              <reciter-card v-bind="reciter" />
            </v-flex>
          </v-layout>
          <v-layout row>
            <v-flex>
              <v-pagination v-model="page" :length="length" circle @input="goToPage"></v-pagination>
            </v-flex>
          </v-layout>
        </template>
        <template v-else>
          <six-card-skeleton />
        </template>
      </v-container>
    </section>
  </div>
</template>

<script>
import ReciterCard from '@/components/ReciterCard.vue';
import SixCardSkeleton from '@/components/SixCardSkeleton.vue';
import { getReciters } from '@/services/reciters';
import { getPopularReciters } from '@/services/popular';

export default {
  name: 'Reciters',
  components: { ReciterCard, SixCardSkeleton },
  async mounted() {
    this.loading = true;
    const [reciters, popularReciters] = await Promise.all([
      getReciters({ per_page: 60 }),
      getPopularReciters({ limit: 6 }),
    ]);
    this.setData(reciters);
    this.setPopularReciters(popularReciters);
    this.loading = false;
  },
  data() {
    return {
      page: 1,
      reciters: null,
      length: 0,
      loading: false,
      popularReciters: [],
    };
  },
  methods: {
    setData(data) {
      this.reciters = data.data.data;
      this.length = data.data.meta.pagination.total_pages;
    },
    setPopularReciters(data) {
      this.popularReciters = data.data.data;
    },
    async goToPage(number) {
      this.loading = true;
      const [reciters] = await Promise.all([getReciters({ per_page: 60, page: number })]);
      this.setData(reciters);
      this.loading = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>
