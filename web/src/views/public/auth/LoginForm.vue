<template>
  <v-form @submit.prevent="submit">
    <v-card class="login-card" :loading="loading">
      <v-card-title>
        <h2 class="card-title">Welcome Back</h2>
      </v-card-title>
      <v-card-text>
        <v-alert type="error" v-if="error">
          I'm an error alert.
        </v-alert>
        <v-text-field outlined label="Email" v-model="email" :error-messages="invalid.email" />
        <v-text-field outlined label="Password" type="password" v-model="password" :error-messages="invalid.password" />
      </v-card-text>
      <v-card-actions>
        <v-btn @click="close" text>Cancel</v-btn>
        <v-spacer></v-spacer>
        <v-btn type="submit" text color="primary" :loading="loading">Log In</v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import HeroBanner from '@/components/HeroBanner.vue';

@Component({
  components: {
    HeroBanner,
  },
})
export default class LoginForm extends Vue {
  private email = '';
  private password = '';
  private error: string|null = null;
  private invalid: any = {};
  private loading = false;

  async submit() {
    this.invalid = {};
    this.loading = true;
    try {
      await this.$store.dispatch('auth/login', { email: this.email, password: this.password });
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
    this.email = '';
    this.password = '';
    this.$emit('close');
  }
}
</script>

<style lang="scss" scoped>
@import "~vuetify/src/styles/styles";
.login-card {
  padding: 0 24px 24px;
}
.card-title {
  text-align: center;
  margin: 24px auto 16px;
  font-weight: 300;
}
</style>
