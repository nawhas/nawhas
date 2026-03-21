<router>
  path: /stories/:date/:story
  name: "stories.show"
</router>

<template>
  <div class="story-page app__section">
    <v-container v-if="story">
      <v-btn text class="mb-4" to="/" exact>
        <v-icon left>
          chevron_left
        </v-icon>
        Home
      </v-btn>
      <h5 class="overline">
        {{ story.date | date }}
      </h5>
      <h1 class="display-1 mb-4">
        {{ story.title }}
      </h1>
      <v-img :src="story.image" max-height="360px" contain class="mb-6" />
      <div class="story-page__body body-1">
        {{ story.body }}
      </div>
    </v-container>
    <v-container v-else>
      <h1 class="headline">
        Story
      </h1>
      <p class="body-1">
        This story could not be found.
      </p>
      <v-btn color="primary" to="/" exact>
        Back to home
      </v-btn>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { Story } from '@/entities/story';

export default Vue.extend({
  computed: {
    story(): Story | undefined {
      const slug = this.$route.params.story;
      return this.$store.getters['stories/story'](slug);
    },
  },
});
</script>

<style lang="scss" scoped>
.story-page__body {
  white-space: pre-line;
}
</style>
