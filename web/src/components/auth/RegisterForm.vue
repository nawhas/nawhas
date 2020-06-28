<template>
  <auth-dialog :loading="loading" @submit="submit" :error="error">
    <template slot="title">Sign Up</template>
    <template slot="message">
      <p class="message-line">
        Create an account on Nawhas.com to
        <br>
        create collections, edit write-ups, and more.
      <p class="message-line">
        Already have an account? <a class="link" href="#">Log in.</a>
      </p>
    </template>
    <v-text-field
        outlined
        autofocus
        label="Name"
        v-model="form.name"
        :error-messages="invalid.name"
    />
    <v-text-field
        outlined
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
      <v-spacer></v-spacer>
      <v-btn @click="close" text>Cancel</v-btn>
      <v-btn
          type="submit"
          elevation="0"
          color="primary"
          :loading="loading"
          :disabled="disabled"
      >
        Sign up
      </v-btn>
    </div>
    <template slot="social">
      <social-login-button type="register" provider="google" />
      <social-login-button type="register" provider="facebook" />
    </template>
  </auth-dialog>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import AuthDialog from '@/components/auth/AuthDialog.vue';
import SocialLoginButton from '@/components/auth/SocialLoginButton.vue';
import { Actions as AuthActions } from '@/store/modules/auth';

interface SignUpFormData {
  name: string;
  email: string;
  password: string;
}

function createForm(): SignUpFormData {
  return { name: '', email: '', password: '' };
}

@Component({
  components: {
    AuthDialog,
    SocialLoginButton,
  },
})
export default class RegisterForm extends Vue {
  private form: SignUpFormData = createForm();
  private error: string | null = null;
  private invalid: any = {};
  private loading = false;

  get disabled() {
    return !this.form.email || !this.form.name || !this.form.password;
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;

    try {
      await this.$store.dispatch(AuthActions.Register, this.form);
      this.close();
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

    return true;
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
