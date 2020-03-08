<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn v-if="track" dark icon v-on="on"><v-icon>edit</v-icon></v-btn>
      <v-btn v-else v-on="on" text>Add Track</v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">{{ track ? 'Edit' : 'Add' }} Track</span>
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
          label="Lyrics"
          v-model="form.lyrics"
          required
        ></v-textarea>
        <v-file-input v-model="form.audio"
                      label="Audio File"
                      placeholder="Upload Track Audio File"
                      prepend-icon="volume_up"
                      outlined
                      accept="audio/*"
                      :show-size="1000"
        >
          <template v-slot:selection="{ text }">
            <v-chip color="deep-orange accent-4" dark label small>
              {{ text }}
            </v-chip>
          </template>
        </v-file-input>
        <div v-cloak @drop.prevent="addFile" @dragover.prevent>
          <h2>Drag and drop track audio here...</h2>
          <p>Make sure to drag and drop one file</p>
          <ul>
            <li v-if="form.audio">
              {{ form.audio.name }} ({{ form.audio.size }})
              <button @click="removeFile" title="Remove">X</button>
            </li>
          </ul>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="error" text @click="confirmDelete">Delete</v-btn>
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
  lyrics: string|null;
  audio: string|Blob|null;
}

const defaults: Form = {
  title: null,
  lyrics: null,
  audio: null,
};

@Component
export default class EditTrackDialog extends Vue {
  @Prop({ type: Object }) private track;
  @Prop({ type: Object }) private album;
  private dialog = false;
  private form: Form = { ...defaults };
  private loading = false;

  get includes() {
    return 'reciter,lyrics,album.tracks,media';
  }

  @Watch('dialog')
  onDialogStateChanged(opened) {
    if (opened) {
      this.resetForm();
    }
  }

  addFile(e) {
    // eslint-disable-next-line prefer-destructuring
    this.form.audio = e.dataTransfer.files[0];
  }

  removeFile() {
    this.form.audio = null;
  }

  resetForm() {
    this.form = { ...defaults };
    if (this.track) {
      const { title, lyrics } = this.track;

      this.form = {
        ...this.form,
        title,
        lyrics: lyrics ? lyrics.content : null,
      };
    }
  }

  async submit() {
    this.loading = true;

    if (this.track) {
      await this.update();
    } else {
      await this.create();
    }

    this.close();
  }

  async create() {
    const data: any = {};
    if (this.form.title) {
      data.title = this.form.title;
    }
    if (this.form.lyrics) {
      data.lyrics = this.form.lyrics;
    }

    const { reciterId } = this.album;
    const albumId = this.album.id;

    let response = await axios.post(
      `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks?include=${this.includes}`,
      data,
    );

    response = await this.uploadAudio(reciterId, albumId, response.data.id) || response;

    this.redirect(response);
  }

  async update() {
    const data: any = {};
    if (this.track.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.track.lyrics?.content !== this.form.lyrics && this.form.lyrics) {
      data.lyrics = this.form.lyrics;
    }

    const { id, reciterId, albumId } = this.track;
    let response = await axios.patch(
      `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}?include=${this.includes}`,
      data,
    );

    response = await this.uploadAudio(reciterId, albumId, id) || response;

    this.redirect(response);
  }

  async uploadAudio(reciterId, albumId, trackId) {
    if (this.form.audio) {
      const upload = new FormData();
      upload.append('audio', this.form.audio);
      return axios.post(
        `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}/media/audio`
        + `?include=${this.includes}`,
        upload,
        { headers: { 'Content-Type': 'multipart/form-data' } },
      );
    }

    return null;
  }

  redirect(response) {
    this.$router.push({
      name: 'tracks.show',
      params: {
        reciter: response.data.reciter.slug,
        album: response.data.year,
        track: response.data.slug,
        trackObject: response.data,
      },
    }).catch(() => window.location.reload());
  }

  async confirmDelete() {
    // eslint-disable-next-line no-alert
    if (window.confirm(`Are you sure you want to delete '${this.track.title}'?`)) {
      const { id, reciterId, albumId } = this.track;
      await axios.delete(
        `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}`,
      );
      this.$router.push({ name: 'reciters.show', params: { reciter: this.track.reciter.slug } });
    }
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }
}
</script>
