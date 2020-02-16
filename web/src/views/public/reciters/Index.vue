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
import { mapGetters } from 'vuex';

export default {
  name: 'Reciters',
  components: { ReciterCard, SixCardSkeleton },
  async mounted() {
    this.loading = true;
    this.$store.dispatch('popular/fetchPopularReciters', { limit: 6 });
    const reciters = await getReciters();
    this.setData(reciters.data.data, reciters.data.meta.pagination.total_pages);
    this.loading = false;
  },
  data() {
    return {
      page: 1,
      reciters: null,
      length: 0,
      loading: false,
    };
  },
  computed: {
    ...mapGetters({
      popularReciters: 'popular/popularReciters',
    }),
  },
  methods: {
    setData(reciters, length = this.length) {
      this.reciters = reciters;
      this.length = length;
    },
    async goToPage(number) {
      this.loading = true;
      const reciters = await getReciters({ page: number });
      this.setData(
        reciters.data.data,
        reciters.data.meta.pagination.total_pages,
      );
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
