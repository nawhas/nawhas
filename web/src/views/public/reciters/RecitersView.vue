<template>
  <div class="view-wrapper">
    <v-container class="app__section">
      <h5 class="section__title">Top Reciters</h5>
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
        <v-btn icon @click="focusSearch"><v-icon>search</v-icon></v-btn>
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
        <v-pagination v-model="page" color="deep-orange"
                      :length="length"
                      circle
                      @input="goToPage"
        ></v-pagination>
      </div>
    </v-container>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import ReciterCard from '@/components/ReciterCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import { getReciters } from '@/services/reciters';
import { getPopularReciters } from '@/services/popular';
import { EventBus, Search } from '@/events';
import goTo from 'vuetify/es5/services/goto';

@Component({
  components: { ReciterCard, SkeletonCardGrid }
})
export default class RecitersPage extends Vue {
  private page = 1;
  private reciters: Array<any>|null = null;
  private length = 1;
  private popularReciters: Array<any>|null = null;

  mounted() {
    getReciters({ per_page: 30, include: 'related' }).then((response) => {
      this.setReciters(response);
    });
    getPopularReciters({ per_page: 6, include: 'related' }).then((response) => {
      this.popularReciters = response.data.data;
    });
  }

  setReciters(data) {
    this.reciters = data.data.data;
    this.length = data.data.meta.pagination.total_pages;
  }

  async goToPage(number) {
    this.$Progress.start();
    goTo(0);
    getReciters({ per_page: 30, include: 'related', page: number }).then((response) => {
      this.setReciters(response);
      this.$Progress.finish();
    });
  }

  focusSearch() {
    this.$nextTick(() => EventBus.$emit(Search.TRIGGER));
  }
}
</script>

<style lang="scss" scoped>
.view-wrapper {
  padding-top: 24px;
}
.pagination {
  padding: 24px;
}
</style>
