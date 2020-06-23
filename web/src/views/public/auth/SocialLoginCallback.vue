<template>
  <div>
    <h1>Logging into {{ provider }}... Please wait.</h1>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { Actions as AuthActions } from '@/store/modules/auth';

@Component
export default class SocialLoginCallback extends Vue {
  get provider() {
    return this.$route.params.provider;
  }

  created() {
    this.callback();
  }

  async callback() {
    await this.$store.dispatch(AuthActions.SocialLoginCallback, {
      provider: this.provider,
      code: this.$route.query.code,
    });
    window.location.href = '/';
  }
}
</script>

<style lang="scss" scoped>
</style>
