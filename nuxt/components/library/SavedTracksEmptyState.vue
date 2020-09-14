<template>
  <div class="saved-tracks-empty">
    <div class="illustration">
      <heart-icon />
    </div>
    <div class="body">
      <h4 class="display-1">
        Keep track of nawhas you love.
      </h4>
      <p class="subtitle-1">
        Add your favorite nawhas to your library to easily find and play them next time!
        As you build your collection, your most recently saved nawhas will show up here.
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'nuxt-property-decorator';
const HeartIcon = require('@/assets/svg/illustrations/heart-icon.svg?inline');

@Component({
  components: {
    HeartIcon,
  },
})
export default class SavedTracksEmptyState extends Vue {

}
</script>

<style lang="scss" scoped>
@import "~assets/theme";
.saved-tracks-empty {
  display: flex;
  flex-direction: row-reverse;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  padding: 36px;
}
.illustration {
  flex: 0 1 160px;
  padding: 0 36px;
}
.body {
  flex: 1 1;
  max-width: 600px;
}
.display-1 {
  font-weight: 300;
  margin-bottom: 16px;
}

@include breakpoint('sm-and-down') {
  .body {
    text-align: center;
  }
  .illustration {
    padding: 24px 0;
  }
}
</style>
