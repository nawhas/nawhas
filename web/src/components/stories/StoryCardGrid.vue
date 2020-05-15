<template>
  <v-row>
    <v-col v-for="(story, index) in stories" :key="index" md="4">
      <story-card :story="story" />
    </v-col>
  </v-row>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import StoryCard from '@/components/stories/StoryCard.vue';

@Component({
  components: {
    StoryCard,
  },
})
export default class StoryCardGrid extends Vue {
  get stories() {
    return this.$store.getters.stories;
  }
}
</script>

<style lang="scss" scoped>

</style>
