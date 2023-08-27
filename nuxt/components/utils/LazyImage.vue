<template>
  <img
    :class="{ lazyload: loading === 'lazy' && !native }"
    :loading="loading"
    v-bind="{ ...attributes }"
  >
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';

const placeholder = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

@Component
export default class LazyImage extends Vue {
  @Prop({ type: String, required: true }) private readonly src!: string;
  @Prop({ type: String, required: false, default: 'lazy' }) private readonly loading!: string;
  private native = false;

  mounted() {
    this.native = 'loading' in HTMLImageElement.prototype;
  }

  get attributes() {
    if (this.loading === 'lazy' && !this.native) {
      return {
        'data-src': this.src,
        'src': placeholder,
      };
    }
    return {
      src: this.src,
    };
  }
}
</script>

<style lang="scss" scoped>

</style>
