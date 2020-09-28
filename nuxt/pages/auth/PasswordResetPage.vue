<router>
  path: /auth/password/reset/:token
  name: "auth.password.reset"
</router>

<template>
  <div class="centered">
    <password-reset-form :user="user" :token="token" class="form" />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { generateMeta } from '@/utils/meta';
import PasswordResetForm from '@/components/auth/PasswordResetForm.vue';
import { User } from '@/entities/user';

interface Data {
  user: User | null;
  token: string | null;
}

export default Vue.extend({
  components: { PasswordResetForm },
  async asyncData({ route, $api, error }) {
    try {
      const { token } = route.params;
      const user = await $api.auth.validateResetToken(token);

      return {
        user,
        token,
      };
    } catch {
      error({ statusCode: 404, message: 'Not found.' });
    }
  },
  data: (): Data => ({
    user: null,
    token: null,
  }),
  watch: {
    '$store.state.auth.user': 'onAuthChange',
  },
  methods: {
    onAuthChange(value) {
      if (value) {
        this.$router.replace('/library/home');
      }
    },
    onSubmit() {

    },
  },
  head: () => generateMeta({
    title: 'Reset Your Password',
  }),
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.centered {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
}
.reset-password {
  padding: 32px 64px;
}
.form {
  width: 100%;
  max-width: 580px;
}

@include breakpoint('sm-and-down') {
  .reset-password {
    padding: 24px;
  }
}
</style>
