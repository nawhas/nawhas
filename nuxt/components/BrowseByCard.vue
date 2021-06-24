<template>
  <div :class="`topic-box-${browseBy} d-flex align-center justify-center white--text`">
    <v-row>
      <v-col class="d-flex justify-center align-center flex-column" md="5" cols="12">
        <div>
          <v-row>
            <v-col>
              <span class="topic-box--left--heading">Browse By</span>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <span class="topic-box--left--subheading">{{ browseBy }}</span>
            </v-col>
          </v-row>
        </div>
      </v-col>
      <v-col class="d-flex justify-center align-center flex-column" md="6">
        <template v-if="dataItem !== null">
          <template v-if="browseBy === 'reciter'">
            <v-row :dense="$vuetify.breakpoint.smAndDown">
              <v-col v-for="reciter in dataItem" :key="reciter.id" sm="6" cols="12">
                <reciter-card featured v-bind="reciter" />
              </v-col>
            </v-row>
          </template>
          <template v-else-if="browseBy === 'topic'">
            <v-row :dense="$vuetify.breakpoint.smAndDown">
              <v-col v-for="topic in dataItem" :key="topic.id" sm="6" cols="12">
                <topic-card :topic="topic" />
              </v-col>
            </v-row>
          </template>
          <v-row>
            <v-col cols="12">
              <v-btn @click="goToViewAllPage()">
                View All
              </v-btn>
            </v-col>
          </v-row>
        </template>
        <template v-else>
          <skeleton-card-grid />
        </template>
      </v-col>
      <v-col md="1" cols="12" />
    </v-row>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import ReciterCard from '@/components/ReciterCard.vue';
import TopicCard from '@/components/topics/TopicCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
@Component({
  components: {
    ReciterCard,
    SkeletonCardGrid,
    TopicCard,
  },
})
export default class BrowseByCard extends Vue {
  @Prop({ required: true })
  readonly browseBy!: 'reciter' | 'topic';

  @Prop({ required: false })
  readonly popularReciters!: Array<any> | null

  @Prop({ required: false })
  readonly topics!: Array<any> | null

  get dataItem(): Array<any> | null {
    if (this.browseBy === 'topic') {
      if (this.browseBy === 'topic' && this.topics !== null) {
        return this.topics;
      }
    }
    if (this.browseBy === 'reciter') {
      if (this.browseBy === 'reciter' && this.popularReciters !== null) {
        return this.popularReciters;
      }
    }
    return null;
  }

  goToViewAllPage() {
    if (this.browseBy === 'topic') {
      this.$router.push({ name: 'topics.index' });
      this.$router.push({ name: 'topics.index' });
    } else if (this.browseBy === 'reciter') {
      this.$router.push({ name: 'reciters.index' });
    }
  }
}
</script>

<style lang="scss" scoped>
.topic-box-reciter {
  min-height: 50vh;
  background: linear-gradient(90deg, rgba(0, 0, 0, 0.73) 3.27%, #172C17 44.77%);
}
.topic-box-topic {
  min-height: 50vh;
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
  text-transform: capitalize;
}
</style>
