<template>
  <button
    class="button"
    type="button"
    :style="styles"
    v-on="$listeners"
  >
    <span class="button__content">
      <google-icon class="button__icon" /> Continue with Google
    </span>
  </button>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
const GoogleIcon = require('@/assets/svg/icon.svg?inline');

@Component({
  components: {
    GoogleIcon,
  },
})
export default class GoogleAuthButton extends Vue {
  @Prop({ type: String, required: true }) private readonly type !: string;

  get styles() {
    const { dark } = this.$vuetify.theme;

    return {
      background: dark ? '#4285F4' : '#fff',
    };
  }
}
</script>

<style lang="scss" scoped>
.button {
  padding: 1px;
  display: block;
  width: 100%;
}
.button__content {
  display: flex;
  align-items: center;
  justify-content: center;
}
.button__icon {
  width: 38px;
  height: 38px;
}
</style>
