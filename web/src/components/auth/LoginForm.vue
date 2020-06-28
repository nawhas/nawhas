<template>
  <auth-dialog :loading="loading" @submit="submit" :error="error">
    <template slot="title">Log In</template>
    <template slot="message">
      <p class="message-line"><strong>Welcome back!</strong> To continue, log into your account.<br></p>
      <p class="message-line">Don't have an account yet? <a class="link" href="#">Sign up.</a></p>
    </template>
    <v-text-field
        outlined
        autofocus
        label="Email"
        type="email"
        v-model="form.email"
        :error-messages="invalid.email"
    />
    <v-text-field
        outlined
        label="Password"
        type="password"
        v-model="form.password"
        :error-messages="invalid.password"
    />
    <div class="actions">
      <div class="forgot-password">
        <a class="link body-2" href="#">Forgot password?</a>
      </div>
      <v-spacer></v-spacer>
      <v-btn @click="close" text>Cancel</v-btn>
      <v-btn
          type="submit"
          elevation="0"
          color="primary"
          :loading="loading"
          :disabled="disabled"
      >
        Log In
      </v-btn>
    </div>
    <template slot="social">
      <social-login-button type="login" provider="google" />
      <social-login-button type="login" provider="facebook" />
    </template>
  </auth-dialog>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import AuthDialog from '@/components/auth/AuthDialog.vue';
import { Actions as AuthActions } from '@/store/modules/auth';
import SocialLoginButton from '@/components/auth/SocialLoginButton.vue';

interface LoginFormData {
  email: string;
  password: string;
}

function createForm(): LoginFormData {
  return { email: '', password: '' };
}

@Component({
  components: {
    SocialLoginButton,
    AuthDialog,
  },
})
export default class LoginForm extends Vue {
  private form: LoginFormData = createForm();
  private error: string|null = null;
  private invalid: any = {};
  private loading = false;

  get disabled() {
    return !this.form.email || !this.form.password;
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;

    try {
      await this.$store.dispatch(AuthActions.Login, this.form);
      this.close();
    } catch (e) {
      if (!e.response) {
        throw e;
      }
      switch (e.response.status) {
        case 401:
          // Credentials do not match.
          this.error = e.response.data.message;
          break;
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

  close() {
    this.form = createForm();
    this.$emit('close');
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
