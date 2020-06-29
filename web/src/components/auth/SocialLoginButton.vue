<template>
  <v-btn
      :outlined="!$vuetify.theme.dark"
      :color="definition.colors.background"
      :light="definition.colors.lightText"
      :dark="!definition.colors.lightText"
      block
      class="social-button"
      @click="onClick"
  >
    <component class="icon" :is="definition.icon"></component>
    <span class="text">{{ definition.text }}</span>
  </v-btn>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { API_DOMAIN } from '@/config';

const providers = (provider, type, dark = false) => (({
  google: {
    text: type === 'login' ? 'Sign in with Google' : 'Sign up with Google',
    icon: () => import('@/assets/social/google.svgx'),
    colors: {
      background: dark ? 'white' : 'secondary',
      lightText: true,
    },
  },
  facebook: {
    text: type === 'login' ? 'Login with Facebook' : 'Continue with Facebook',
    icon: dark
      ? () => import('@/assets/social/facebook-white.svgx')
      : () => import('@/assets/social/facebook.svgx'),
    colors: {
      background: dark ? '#1877F2' : 'secondary',
      lightText: !dark,
    },
  },
})[provider]);

@Component
export default class SocialLoginButton extends Vue {
  @Prop({ type: String, required: true }) private readonly type !: string;
  @Prop({ type: String, required: true }) private readonly provider !: string;

  get definition() {
    return providers(this.provider, this.type, this.$vuetify.theme.dark);
  }

  onClick() {
    let domain = API_DOMAIN;
    if (domain !== undefined && !domain.startsWith('http')) {
      domain = window.location.origin + domain;
    }

    window.location.assign(`${domain}/oauth/${this.provider}`);
  }
}
</script>

<style lang="scss" scoped>
.icon {
  width: 18px;
  height: 18px;
  margin-right: 24px;
}
.social-button {
  margin-bottom: 12px;
}
.text {
  font-family: "Roboto", sans-serif;
  font-weight: 500;
  text-transform: none;
  letter-spacing: normal;
}
</style>
