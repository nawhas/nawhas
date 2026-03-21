<template>
  <v-row>
    <v-col v-for="story in resolvedStories" :key="story.id" md="4">
      <story-card :story="story" />
    </v-col>
  </v-row>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import StoryCard from '@/components/stories/StoryCard.vue';
import { Story } from '@/entities/story';

@Component({
  components: {
    StoryCard,
  },
})
export default class StoryCardGrid extends Vue {
  @Prop({ type: Array, default: null }) readonly stories!: Array<Story> | null;

  get resolvedStories(): Array<Story> {
    if (this.stories != null) {
      return this.stories;
    }
    return this.$store.getters['stories/stories'] as Array<Story>;
  }
}
</script>
