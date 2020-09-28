<template>
  <auth-dialog :loading="loading" :error="error" @submit="submit">
    <template slot="title">
      Reset Password
    </template>
    <template slot="message">
      <p class="message-line">
        Let's get you back into your account. Enter the email address you used when creating your account.
      </p>
    </template>

    <div v-if="success">
      <v-alert type="success" outlined>
        If we have an account matching your email address, we'll send you an email with instructions on
        how to reset your password. Keep an eye on your inbox!
      </v-alert>
      <div class="text-center">
        <v-btn color="primary" @click="close">
          Close
        </v-btn>
      </div>
    </div>
    <div v-else>
      <v-text-field
        v-model="form.email"
        outlined
        autofocus
        label="Email"
        type="email"
        :error-messages="invalid.email"
      />
      <div class="actions">
        <v-spacer />
        <v-btn text @click="close">
          Cancel
        </v-btn>
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
    </div>
  </auth-dialog>
</template>

<script lang="ts">
import { Component, Vue } from 'nuxt-property-decorator';
import { PasswordResetEmailPayload } from '@/api/auth';
import AuthDialog from '@/components/auth/AuthDialog.vue';

function createForm(): PasswordResetEmailPayload {
  return { email: '' };
}

@Component({
  components: {
    AuthDialog,
  },
})
export default class RequestPasswordResetForm extends Vue {
  private form: PasswordResetEmailPayload = createForm();
  private success: boolean = false;
  private error: string|null = null;
  private invalid: any = {};
  private loading = false;

  get disabled() {
    return !this.form.email;
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;

    try {
      await this.$api.auth.sendResetPasswordEmail(this.form);
      this.success = true;
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

  close() {
    this.form = createForm();
    this.invalid = {};
    this.error = null;
    this.success = false;
    this.$emit('close');
  }

  switchToLoginDialog() {
    const prompt = this.$store.state.auth.prompt ?? {};
    this.$store.commit('auth/PROMPT_USER', {
      ...prompt,
      type: 'login',
    });
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
