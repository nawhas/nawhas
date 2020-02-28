<template>
    <div class="audio-player">
      <div class="artwork">
        <img src="/img/default-album-image.png" />
      </div>
      <div class="">
      </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class AudioPlayer extends Vue {

}
</script>

<style lang="scss" scoped>

.audio-player {
    width: 100%;
    height: 80px;
    background: white;
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 10;
    box-shadow: 0 -2px 8px 4px rgba(0,0,0,0.16);

    .artwork img {
      width: 80px;
    }
}
</style>
