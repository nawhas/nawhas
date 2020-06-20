<template>
  <v-form @submit.prevent="submit">
    <v-card class="login-card" :loading="loading">
      <v-card-title>
        <h2 class="card-title">Sign Up</h2>
      </v-card-title>
      <v-card-text>
        <v-alert type="error" v-if="error" outlined class="mb-6">{{ error }}</v-alert>
        <v-text-field outlined label="Name" v-model="name" :error-messages="invalid.name" />
        <v-text-field outlined label="Email" type="email" v-model="email" :error-messages="invalid.email" />
        <v-text-field
          outlined
          label="Password"
          type="password"
          v-model="password"
          :error-messages="invalid.password"
        />
        <v-text-field
          outlined
          label="Confirm Password"
          type="password"
          v-model="confirmPassword"
          :error-messages="invalid.confirmPassword"
        />
        <v-text-field
          outlined
          label="Nickname (optional)"
          v-model="nickname"
          :error-messages="invalid.nickname"
        />
      </v-card-text>
      <v-card-actions>
        <v-btn @click="close" text>Cancel</v-btn>
        <v-spacer></v-spacer>
        <v-btn type="submit" :disabled="!canSubmit" text color="primary" :loading="loading">Sign Up</v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import HeroBanner from '@/components/HeroBanner.vue';
import { Actions as AuthActions } from '@/store/modules/auth';

@Component({
  components: {
    HeroBanner,
  },
})
export default class RegisterForm extends Vue {
  private name = '';
  private email = '';
  private password = '';
  private confirmPassword = '';
  private nickname: null|string = null;
  private error: string | null = null;
  private invalid: any = {};
  private loading = false;

  get canSubmit() {
    if (!this.email || !this.name || !this.password || !this.confirmPassword) {
      return false;
    }

    return true;
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;

    if (this.password !== this.confirmPassword) {
      this.invalid.confirmPassword = 'The password confirmation does not match.';
      this.loading = false;
      return false;
    }

    try {
      await this.$store.dispatch(AuthActions.Register, {
        name: this.name,
        email: this.email,
        password: this.password,
        nickname: this.nickname,
      });
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
    this.name = '';
    this.email = '';
    this.password = '';
    this.confirmPassword = '';
    this.nickname = '';
    this.$emit('close');
  }
}
</script>

<style lang="scss" scoped>
@import '~vuetify/src/styles/styles';
.login-card {
  padding: 0 24px 24px;
}
.card-title {
  text-align: center;
  margin: 24px auto 16px;
  font-weight: 300;
}
</style>
