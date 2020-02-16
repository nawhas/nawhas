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
              <v-pagination
                v-model="page"
                :length="recitersPaginationLength"
                circle
                @input="goToPage"
              ></v-pagination>
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
import { mapGetters } from 'vuex';

export default {
  name: 'Reciters',
  components: { ReciterCard, SixCardSkeleton },
  mounted() {
    this.$store.dispatch('popular/fetchPopularReciters', { limit: 6 });
    this.$store.dispatch('reciters/fetchReciters', { page: 1 });
  },
  data() {
    return {
      page: 1,
    };
  },
  computed: {
    ...mapGetters({
      popularReciters: 'popular/popularReciters',
      reciters: 'reciters/reciters',
      recitersPaginationLength: 'reciters/recitersPaginationLength',
    }),
  },
  methods: {
    goToPage(number) {
      this.$store.dispatch('reciters/fetchReciters', { page: number });
    },
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>
