<template>
  <div class="container">
    <div class="header">
      <div class="header__time">Time</div>
      <div class="header__text">Text</div>
      <div class="header__repeat">Repeat</div>
      <div class="header__type">Type</div>
    </div>
  </div>
</template>

<script lang="ts">
import {
  Component, Vue,
} from 'vue-property-decorator';

@Component
export default class AddLyrics extends Vue {
}
</script>

<style lang="scss" scoped>
.header {
  display: flex;
  flex-direction: row;
  background-color: #292929;
  padding: 10px;

  .header__time {
    flex: 1 0 0;
  }

  .header__text {
    flex: 3 0 0;
  }

  .header__repeat {
    flex: 1 0 0;
  }

  .header__type {
    flex: 1 0 0;
  }
}

.lyrics_content {
  background: #1E1E1E;
}
</style>
