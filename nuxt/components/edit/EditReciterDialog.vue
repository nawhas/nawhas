<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on">
        Edit
      </v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">Edit Reciter</span>
      </v-card-title>
      <v-card-text class="py-4">
        <v-text-field
          v-model="form.name"
          outlined
          label="Name"
          required
        />
        <v-textarea
          v-model="form.description"
          outlined
          label="Description"
        />
        <v-file-input
          v-model="form.avatar"
          label="Avatar"
          placeholder="Upload an Avatar"
          prepend-icon="mdi-camera"
          outlined
          accept="image/*"
          :show-size="1000"
        >
          <template v-slot:selection="{ index, text }">
            <v-chip
              v-if="index < 2"
              color="deep-orange accent-4"
              dark
              label
              small
            >
              {{ text }}
            </v-chip>
          </template>
        </v-file-input>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">
          Cancel
        </v-btn>
        <v-btn color="primary" text :loading="loading" @click="submit">
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Client from '@/services/client';
import {
  Component, Prop, Watch, Vue,
} from 'vue-property-decorator';

interface Form {
  name: string|null;
  description: string|null;
  avatar: string|Blob|null;
}
const defaults: Form = {
  name: null,
  description: null,
  avatar: null,
};
@Component
export default class EditReciterDialog extends Vue {
  @Prop({ type: Object }) private reciter;
  private dialog = false;
  private form: Form = { ...defaults };
  private loading = false;
  @Watch('dialog')
  onDialogStateChanged(opened) {
    if (opened) {
      this.resetForm();
    }
  }

  resetForm() {
    const { name, description } = this.reciter;
    this.form = {
      ...defaults,
      name,
      description,
    };
  }

  async submit() {
    this.loading = true;
    const data: any = {};
    if (this.reciter.name !== this.form.name && this.form.name) {
      data.name = this.form.name;
    }
    if (this.reciter.description !== this.form.description && this.form.description) {
      data.description = this.form.description;
    }
    const response = await Client.patch(`/v1/reciters/${this.reciter.id}`, data);
    const { slug } = response.data;
    if (this.form.avatar) {
      const imageFormData = new FormData();
      imageFormData.append('avatar', this.form.avatar);
      await Client.post(
        `/v1/reciters/${this.reciter.id}/avatar`,
        imageFormData,
        { headers: { 'Content-Type': 'multipart/form-data' } },
      );
    }
    this.$router.replace({ name: 'reciters.show', params: { reciter: slug } })
      .catch(() => window.location.reload());
    this.close();
    this.loading = false;
  }

  close() {
    this.dialog = false;
  }
}
</script>
