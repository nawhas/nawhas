<template>
  <v-card class="releases">
    <v-card-title class="releases__title">
      What's New
    </v-card-title>
    <v-card-text class="releases__list">
      <div class="release">
        <div class="release__date overline">
          v2.0.0 &mdash; April 8th
        </div>
        <div class="release__title">
          Synchronized write-ups with audio playback
        </div>
        <div class="release__description">
          The upgraded audio player now features a brand new Write-Up view, letting you read the write-up for
          supported nawhas in real-time synchronization with audio playback. Try it out on
          <router-link to="/reciters/tejani-brothers/albums/2019" @click.native="$emit('close')">
            Tejani Brother's 2019 album.
          </router-link>
          More nawhas will be upgraded to support this upgraded experience in the coming weeks!
        </div>
      </div>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="$emit('close')">
          Dismiss
        </v-btn>
      </v-card-actions>
    </v-card-text>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

interface Release {
  version: string;
  date: string;
  title: string;
  description: string;
}

@Component
export default class AppChangelog extends Vue {

}
</script>

<style lang="scss" scoped>
.releases__title {
  font-size: 28px !important;
  line-height: normal !important;
  font-weight: 300 !important;
  justify-content: center;
  padding: 24px !important;
}

.releases__list {
  max-height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
}
.release {
  padding: 4px 0;
  margin-bottom: 12px;
}
.release__title {
  font-size: 18px;
  padding: 6px 0;
}
.release__description {
  line-height: 1.4rem;
  ::v-deep a {
    text-decoration: none;
  }
}
</style>
