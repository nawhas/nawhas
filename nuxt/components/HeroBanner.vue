<template>
  <div :class="['hero', 'hero--' + $vuetify.breakpoint.name]" :style="style">
    <div class="hero__content">
      <slot>
        Add your content here.
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeroBanner',
  props: {
    background: {
      type: String,
      required: true,
    },
    opacity: {
      type: Number,
      required: false,
      default: 0.63,
    },
  },
  computed: {
    style() {
      const gradient = `rgba(0, 0, 0, ${this.opacity})`;
      return `background-image: linear-gradient(${gradient}, ${gradient}), url('${this.background}')`;
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
