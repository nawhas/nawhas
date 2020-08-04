<template>
  <v-card class="bug-report-form" :loading="loading">
    <v-form @submit.prevent="submit">
      <v-card-title>
        <h2 class="card-title">
          Report an Issue
        </h2>
      </v-card-title>
      <v-card-text>
        <p class="body-2 mb-8">
          Is something not working quite right? Can we do something better? We'd love to hear from you!
        </p>
        <v-alert v-if="error" type="error" outlined class="mb-6">
          {{ error }}
        </v-alert>
        <v-select
          v-model="type"
          outlined
          label="Type"
          :items="types"
        />
        <v-text-field
          v-model="summary"
          outlined
          label="Summary"
          hint="Provide a brief summary of the issue."
          counter="60"
          :error-messages="invalid.summary"
        />
        <v-textarea
          v-model="details"
          outlined
          label="Details"
          :error-messages="invalid.details"
        />
        <p>
          In case we need to contact you for further information, please provide your email address. This is optional.
        </p>
        <v-text-field
          v-model="email"
          outlined
          label="Email (optional)"
          :error-messages="invalid.email"
        />
      </v-card-text>
      <v-card-actions>
        <v-btn text @click="close">
          Cancel
        </v-btn>
        <v-spacer />
        <v-btn type="submit" text color="primary" :loading="loading">
          Submit
        </v-btn>
      </v-card-actions>
    </v-form>
  </v-card>
</template>

<script lang="ts">
import Vue from 'vue';
import { Component } from 'nuxt-property-decorator';
import client from '@/services/client';
import { showToast } from '@/events/toaster';

type IssueType = 'bug' | 'feature' | 'general';
interface IssueTypeOption {
  text: string;
  value: IssueType;
}

@Component
export default class BugReportForm extends Vue {
  private type: IssueType = 'bug';
  private summary = '';
  private details = '';
  private email = '';
  private error: string|null = null;
  private invalid: any = {};
  private loading = false;

  get types(): Array<IssueTypeOption> {
    return [
      { text: 'Issue', value: 'bug' },
      { text: 'Feature Request', value: 'feature' },
      { text: 'General Feedback', value: 'general' },
    ];
  }

  async submit() {
    this.invalid = {};
    this.error = null;
    this.loading = true;
    try {
      await client.post('/v1/app/feedback', {
        summary: this.summary,
        type: this.type,
        details: this.details,
        email: this.email,
      });

      showToast({ text: 'Your feedback has been submitted!' });
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
    this.type = 'bug';
    this.summary = '';
    this.details = '';
    this.email = '';
    this.invalid = {};
    this.error = null;
    this.$emit('close');
  }
}
</script>

<style lang="scss" scoped>
@import "~vuetify/src/styles/styles";
.bug-report-form {
  padding: 0 24px 24px;
}
.card-title {
  text-align: center;
  margin: 24px auto 16px;
  font-weight: 300;
}
</style>
