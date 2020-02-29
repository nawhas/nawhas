<template>
  <div :class="['hero', 'hero--' + $vuetify.breakpoint.name]" v-bind:style="style">
    <div class="hero__content">
      <slot>
        Add your content here.
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'hero-banner',
  props: ['background'],
  computed: {
    style() {
      return `background-image: linear-gradient(rgba(0, 0, 0, 0.63), rgba(0, 0, 0, 0.63)), url('${this.background}')`;
    },
  },
};
</script>

<style lang="scss" scoped>
.hero {
  background-color: gray;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  height: auto;
}
.hero--lg, .hero--xl {
  min-height: 350px;
}
.hero--md {
  min-height: 300px;
}
</style>
