<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn v-if="album" dark text v-on="on">
        Edit
      </v-btn>
      <v-btn v-else text v-on="on">
        Add Album
      </v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">{{ album ? 'Edit' : 'Create' }} Album</span>
      </v-card-title>
      <v-card-text class="py-4">
        <v-text-field
          v-model="form.title"
          outlined
          label="Name"
          required
        />
        <v-text-field
          v-model="form.year"
          outlined
          label="Release Year"
          required
        />
        <v-file-input
          v-model="form.artwork"
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
              dark
              label
              small
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
              <button title="Remove" @click="removeFile">
                X
              </button>
            </li>
          </ul>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-btn v-if="album" color="error" text @click="confirmDelete">
          Delete
        </v-btn>
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
import {
  Component, Prop, Watch, Vue,
} from 'nuxt-property-decorator';

interface Form {
  title: string|null;
  year: string|null;
  artwork: File|null;
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
    window.location.reload();
  }

  async create() {
    const data: any = {};
    data.title = this.form.title;
    data.year = this.form.year;
    const response = await this.$api.albums.store(this.reciter.id, data);
    await this.uploadArtwork(this.reciter.id, response.id);
  }

  async update() {
    const data: any = {};
    if (this.album.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.album.year !== this.form.year && this.form.year) {
      data.year = this.form.year;
    }
    await this.$api.albums.update(this.album.reciterId, this.album.id, data);
    await this.uploadArtwork(this.album.reciterId, this.album.id);
  }

  async uploadArtwork(reciterId, albumId) {
    if (this.form.artwork) {
      await this.$api.albums.changeArtwork(reciterId, albumId, this.form.artwork);
    }
  }

  async confirmDelete() {
    // eslint-disable-next-line no-alert
    if (window.confirm(`Are you sure you want to delete '${this.album.title} - ${this.album.year}'?`)) {
      const { id, reciterId } = this.album;
      await this.$api.albums.delete(reciterId, id);
      window.location.reload();
    }
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }
}
</script>
