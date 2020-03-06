<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on">Edit</v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">Edit {{ album.title }}</span>
      </v-card-title>
      <v-card-text class="py-4">
        <v-text-field
          outlined
          v-model="form.title"
          label="Name"
          required
        ></v-text-field>
        <v-textarea
          outlined
          label="Description"
          v-model="form.year"
        ></v-textarea>
        <v-file-input v-model="form.artwork"
                      label="Artwork"
                      placeholder="Upload Album Artwork"
                      prepend-icon="mdi-camera"
                      outlined
                      accept="image/*"
                      :show-size="1000"
        >
          <template v-slot:selection="{ index, text }">
            <v-chip
              v-if="index < 2"
              color="deep-orange accent-4"
              dark label small
            >
              {{ text }}
            </v-chip>
          </template>
        </v-file-input>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="close">Cancel</v-btn>
        <v-btn color="primary" text @click="submit" :loading="loading">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import axios from 'axios';
import {
  Component, Prop, Watch, Vue,
} from 'vue-property-decorator';
import { API_DOMAIN } from '@/config';

interface Form {
  title: string|null;
  year: string|null;
  artwork: string|Blob|null;
}

const defaults: Form = {
  title: null,
  year: null,
  artwork: null,
};

@Component
export default class EditAlbumDialog extends Vue {
  @Prop({ type: Object }) private album;
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
    const { title, year } = this.album;
    this.form = {
      ...defaults,
      title,
      year,
    };
  }

  async submit() {
    this.loading = true;
    const data: any = {};
    if (this.album.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.album.year !== this.form.year && this.form.year) {
      data.year = this.form.year;
    }

    await axios.patch(
      `${API_DOMAIN}/v1/reciters/${this.album.reciterId}/albums/${this.album.id}`,
      data,
    );

    if (this.form.artwork) {
      const upload = new FormData();
      upload.append('artwork', this.form.artwork);
      await axios.post(
        `${API_DOMAIN}/v1/reciters/${this.album.reciterId}/albums/${this.album.id}/artwork`,
        upload,
        { headers: { 'Content-Type': 'multipart/form-data' } },
      );
    }
    this.close();
  }

  close() {
    this.dialog = false;
  }
}
</script>
