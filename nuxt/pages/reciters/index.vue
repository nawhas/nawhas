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
        <v-btn icon>
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
        />
      </div>
    </v-container>
  </div>
</template>

<script lang="ts">
import {
  defineComponent,
  reactive,
  toRef,
  toRefs,
  useContext,
  useFetch,
  useMeta,
  watch,
} from 'nuxt-composition-api';
import ReciterCard from '@/components/ReciterCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import { Reciter, ReciterIncludes } from '@/api/reciters';
import useRouter from '~/use/router';

export default defineComponent({
  head: {},

  components: {
    ReciterCard,
    SkeletonCardGrid,
  },

  setup() {
    const { router } = useRouter();
    const { $api, query } = useContext();
    useMeta({ title: 'Reciters' });

    const data = reactive({
      reciters: null as Array<Reciter> | null,
      popularReciters: null as Array<Reciter> | null,
      page: Number(query.value.page || 1),
      length: 1,
    });

    const { fetch } = useFetch(async () => {
      const promises: Array<Promise<any>> = [];

      promises.push(
        $api.reciters.index({
          include: [ReciterIncludes.related],
          pagination: { limit: 30, page: data.page },
        }).then((response) => {
          data.reciters = response.data;
          data.length = response.meta.pagination.total_pages;
        }),
      );

      if (data.popularReciters === null) {
        promises.push(
          $api.reciters.popular({
            include: [ReciterIncludes.related],
            pagination: { limit: 6 },
          }).then((response) => {
            data.popularReciters = response.data;
          }),
        );
      }

      await Promise.all(promises);
    });

    watch(toRef(data, 'page'), (value) => {
      router.push({ query: { page: String(value) } });
    });
    watch(query, fetch);

    return { ...toRefs(data) };
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
