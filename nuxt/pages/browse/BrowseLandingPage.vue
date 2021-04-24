<router>
  path: /browse
  name: "browse.index"
</router>

<template>
  <div>
    <div class="topic-box-reciter d-flex align-center justify-center">
      <v-row>
        <v-col class="d-flex justify-center align-center" md="5" cols="12">
          <div class="d-inline">
            <span class="topic-box--left--heading">Browse By</span>
            <span class="topic-box--left--subheading">Reciter</span>
          </div>
        </v-col>
        <v-col class="d-flex justify-center align-center" md="6">
          <template v-if="popularReciters">
            <v-row :dense="$vuetify.breakpoint.smAndDown">
              <v-col v-for="reciter in popularReciters" :key="reciter.id" sm="6" cols="12">
                <reciter-card featured v-bind="reciter" />
              </v-col>
            </v-row>
          </template>
          <template v-else>
            <skeleton-card-grid />
          </template>
        </v-col>
        <v-col md="1" cols="12"></v-col>
      </v-row>
    </div>

    <div class="topic-box-topic d-flex align-center justify-center">
      <v-row>
        <v-col class="d-flex justify-center align-center" md="5" cols="12">
          <div class="d-inline">
            <span class="topic-box--left--heading">Browse By</span>
            <span class="topic-box--left--subheading">Topic</span>
          </div>
        </v-col>
        <v-col class="d-flex justify-center align-center" md="6">
          <template v-if="popularReciters">
            <v-row :dense="$vuetify.breakpoint.smAndDown">
              <v-col v-for="reciter in popularReciters" :key="reciter.id" sm="6" cols="12">
                <reciter-card featured v-bind="reciter" />
              </v-col>
            </v-row>
          </template>
          <template v-else>
            <skeleton-card-grid />
          </template>
        </v-col>
        <v-col md="1" cols="12"></v-col>
      </v-row>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import ReciterCard from '@/components/ReciterCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import { ReciterIncludes } from '@/api/reciters';
import { generateMeta } from '@/utils/meta';

interface Data {
  popularReciters: Array<any> | null;
  topics: Array<any> | null;
}

export default Vue.extend({
  components: {
    ReciterCard,
    SkeletonCardGrid,
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
.topic-box-reciter {
  height:50vh;
  background: linear-gradient(90deg, rgba(0, 0, 0, 0.73) 3.27%, #172C17 44.77%);
}

.topic-box-topic {
  height:50vh;
  background: linear-gradient(90deg, rgba(0, 0, 0, 0.73) 3.27%, #171B2C 44.77%);
}

.topic-box--left {
  font-family: Roboto, serif;
  font-style: normal;
  color: #FFFFFF;
}

.topic-box--left--heading {
  font-weight: normal;
  font-size: 20px;
  line-height: 16px;
  /* identical to box height, or 80% */

  letter-spacing: 1.5px;
  text-transform: uppercase;
}

.topic-box--left--subheading {
  font-weight: 300;
  font-size: 72px;
  line-height: 84px;
}
</style>
