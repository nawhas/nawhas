<template>
  <auth-dialog :loading="loading" :error="error" @submit="submit">
    <template slot="title">
      Reset Password
    </template>
    <template slot="message">
      <p class="message-line">
        Welcome back, {{ user.name }}! Choose a new password.
      </p>
    </template>
    <v-text-field
      :value="user.email"
      outlined
      disabled
      label="Email"
      type="email"
    />
    <v-text-field
      v-model="form.password"
      outlined
      autofocus
      label="New Password"
      type="password"
      :error-messages="invalid.password"
    />
    <div class="actions">
      <v-spacer />
      <v-btn
        type="submit"
        elevation="0"
        color="primary"
        :loading="loading"
        :disabled="disabled"
      >
        Submit
      </v-btn>
    </div>
  </auth-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import AuthDialog from '@/components/auth/AuthDialog.vue';
import { User } from '@/entities/user';
import { showToast } from '@/events/toaster';

interface PasswordResetFormData {
  password: string;
}
function createForm(): PasswordResetFormData {
  return {
    password: '',
  };
}

@Component({
  components: {
    AuthDialog,
  },
})
export default class PasswordResetForm extends Vue {
  @Prop({ type: Object, required: true }) private readonly user!: User;
  @Prop({ type: String, required: true }) private readonly token!: string;
  private form: PasswordResetFormData = createForm();
  private error: string|null = null;
  private invalid: any = {};
  private loading = false;

  get disabled() {
    return !this.form.password;
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;

    try {
      await this.$api.auth.resetPassword({
        ...this.form,
        token: this.token,
      });
      await this.$store.dispatch('auth/reinitialize');
      showToast({
        text: 'Your password has been changed!',
        type: 'success',
      });
      await this.$router.push('/');
    } catch (e) {
      if (!e.response) {
        throw e;
      }
      switch (e.response.status) {
        case 422:
          // Invalid form submission
          this.invalid = e.response.data.errors;
          break;
        default:
          throw e;
      }
    } finally {
      this.loading = false;
    }
  }
}
</script>

<style lang="scss" scoped>
.message-line {
  margin: 4px 0;
}
.link {
  text-decoration: none;
}
.actions {
  display: flex;
  align-items: center;
}
</style>
