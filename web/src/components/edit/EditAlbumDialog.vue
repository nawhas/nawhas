<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on" v-if="album">Edit</v-btn>
      <v-btn text v-on="on" v-else>Add Album</v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">{{ album ? 'Edit' : 'Create' }} Album</span>
      </v-card-title>
      <v-card-text class="py-4">
        <v-text-field
          outlined
          v-model="form.title"
          label="Name"
          required
        ></v-text-field>
        <v-text-field
          outlined
          label="Release Year"
          v-model="form.year"
          required
        ></v-text-field>
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
        <div v-cloak @drop.prevent="addFile" @dragover.prevent>
          <h2>Drag and drop album artwork here...</h2>
          <p>Make sure to drag and drop one file</p>
          <ul>
            <li v-if="form.artwork">
              {{ form.artwork.name }} ({{ form.artwork.size }})
              <button @click="removeFile" title="Remove">X</button>
            </li>
          </ul>
        </div>
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
import Client from '@/services/client';
import {
  Component, Prop, Watch, Vue,
} from 'vue-property-decorator';

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
  @Prop({ type: Object }) private reciter;
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
  addFile(e) {
    const file = e.dataTransfer.files[0];
    if (file.type.match(/image.*/)) {
      // eslint-disable-next-line prefer-destructuring
      this.form.artwork = file;
    }
  }
  removeFile() {
    this.form.artwork = null;
  }
  resetForm() {
    this.form = { ...defaults };
    if (this.album) {
      const { title, year } = this.album;
      this.form = {
        ...this.form,
        title,
        year,
      };
    }
  }
  async submit() {
    this.loading = true;
    if (this.album) {
      await this.update();
    } else {
      await this.create();
    }
    this.close();
    this.$router.replace({ name: 'reciters.show', params: { reciter: this.reciter.slug } })
      .catch(() => window.location.reload());
  }
  async create() {
    const data: any = {};
    data.title = this.form.title;
    data.year = this.form.year;
    const response = await Client.post(
      `/v1/reciters/${this.reciter.id}/albums`,
      data,
    );
    await this.uploadArtwork(this.reciter.id, response.data.id);
  }
  async update() {
    const data: any = {};
    if (this.album.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.album.year !== this.form.year && this.form.year) {
      data.year = this.form.year;
    }
    await Client.patch(
      `/v1/reciters/${this.album.reciterId}/albums/${this.album.id}`,
      data,
    );
    await this.uploadArtwork(this.album.reciterId, this.album.id);
  }
  async uploadArtwork(reciterId, albumId) {
    if (this.form.artwork) {
      const upload = new FormData();
      upload.append('artwork', this.form.artwork);
      await Client.post(
        `/v1/reciters/${reciterId}/albums/${albumId}/artwork`,
        upload,
        { headers: { 'Content-Type': 'multipart/form-data' } },
      );
    }
  }
  close() {
    this.dialog = false;
    this.loading = false;
  }
}
</script>
