<template>
  <div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="popularReciters">
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
        <template v-if="reciters">
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
  mounted() {
    getReciters({ per_page: 60 }).then((response) => {
      this.setReciters(response);
    });
    getPopularReciters({ limit: 6 }).then((response) => {
      this.popularReciters = response.data.data;
    });
  },
  data() {
    return {
      page: 1,
      reciters: null,
      length: 0,
      popularReciters: null,
    };
  },
  methods: {
    setReciters(data) {
      this.reciters = data.data.data;
      this.length = data.data.meta.pagination.total_pages;
    },
    async goToPage(number) {
      this.reciters = null;
      getReciters({ per_page: 60, page: number }).then((response) => {
        this.setReciters(response);
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>
