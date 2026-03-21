<template>
  <v-card class="story" :to="route">
    <v-img :src="story.image" height="200px" />
    <v-card-text class="text--primary">
      <h5 class="overline">
        {{ story.date | date }}
      </h5>
      <h3 class="title">
        {{ story.title }}
      </h3>
      <p class="body" v-html="story.body" />
    </v-card-text>
    <v-card-actions class="actions">
      <v-btn text color="primary">
        Read more
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import { RawLocation } from 'vue-router';
import { Story } from '@/entities/story';

@Component
export default class StoryCard extends Vue {
  @Prop({ type: Object, required: true }) private readonly story!: Story;

  get route(): RawLocation {
    return {
      name: 'stories.show',
      params: {
        date: this.story.date,
        story: this.story.slug,
      },
    };
  }
}
</script>

<style lang="scss" scoped>
.story .body {
  --line-height: 1.2rem;
  --max-lines: 3;
  line-height: var(--line-height);
  height: calc(var(--line-height) * var(--max-lines));
  overflow: hidden;
  opacity: 0.65;
  margin: 6px 0 0;
}
.story .actions {
  padding-top: 0;
}
</style>
