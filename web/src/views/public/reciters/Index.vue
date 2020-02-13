<template>
  <div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <v-layout row wrap>
          <v-flex xs12 sm6 md4 v-for="reciter in popularReciters" :key="reciter.id">
            <reciter-card featured v-bind="reciter" />
          </v-flex>
        </v-layout>
      </v-container>
    </section>

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">All Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <v-layout row wrap>
          <v-flex xs12 sm6 md4 v-for="reciter in reciters" :key="reciter.id">
            <reciter-card v-bind="reciter" />
          </v-flex>
        </v-layout>
      </v-container>
    </section>
  </div>
</template>

<script>
import ReciterCard from '@/components/ReciterCard.vue';
import { mapGetters } from 'vuex';

export default {
  name: 'Reciters',
  components: { ReciterCard },
  mounted() {
    this.$store.dispatch('popular/fetchPopularReciters', { limit: 6 });
    this.$store.dispatch('reciters/fetchReciters');
  },
  computed: {
    ...mapGetters({
      popularReciters: 'popular/popularReciters',
      reciters: 'reciters/reciters',
    }),
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>
