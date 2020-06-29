<template>
  <v-card class="auth-dialog" :loading="loading">
    <div class="auth-dialog__content">
      <div class="auth-dialog__header">
        <div class="auth-dialog__icon-container">
          <logo-icon class="auth-dialog__icon" />
        </div>
        <h2 class="auth-dialog__title">
          <slot name="title">Authenticate</slot>
        </h2>
      </div>
      <div class="auth-dialog__message">
        <slot name="message"></slot>
      </div>
      <v-form @submit.prevent="$emit('submit')">
        <v-alert type="error" v-if="error" outlined class="mb-6">
          {{ error }}
        </v-alert>
        <slot></slot>
      </v-form>
      <div class="auth-dialog__social" v-show="socialAuthEnabled">
        <labeled-divider label="or" />
        <slot name="social"></slot>
      </div>
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import LogoIcon from '@/assets/icon.svgx';
import LabeledDivider from '@/components/ui/LabeledDivider.vue';
import { Getters as FeatureGetters } from '@/store/modules/features';
import { SOCIAL_AUTHENTICATION } from '@/entities/features';

@Component({
  components: {
    LabeledDivider,
    LogoIcon,
  },
})
export default class AuthDialog extends Vue {
  @Prop({ type: Boolean }) private readonly loading !: boolean;
  @Prop({ type: String }) private readonly error !: string;


  get socialAuthEnabled() {
    return this.$store.getters[FeatureGetters.Enabled](SOCIAL_AUTHENTICATION);
  }
}
</script>

<style lang="scss" scoped>
.auth-dialog__content {
  padding: 32px 64px;
}
.auth-dialog__header {
  padding: 0 0 12px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.auth-dialog__icon-container {
  $size: 48px;

  height: $size;
  width: $size;
  position: relative;

  .auth-dialog__icon {
    height: $size;
    position: relative;
  }

  &::before {
    content: '';
    height: $size;
    width: $size;
    border-radius: 24px;
    box-shadow: inset 0 0 6px 6px rgba(0,0,0,0.05);
    z-index: 1;
    position: absolute;
    top: 0;
    left: 0;
  }
}
.auth-dialog__title {
  font-size: 34px;
  font-weight: 300;
}
.auth-dialog__message {
  text-align: center;
  padding: 0 0 16px;
}
</style>
