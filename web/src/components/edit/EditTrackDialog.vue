<template>
  <v-dialog v-model="dialog"
            persistent fullscreen no-click-animation hide-overlay
            transition="dialog-bottom-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn v-if="track" dark icon v-on="on"><v-icon>edit</v-icon></v-btn>
      <v-btn v-else v-on="on" text>Add Track</v-btn>
    </template>
    <v-card :loading="loading">
      <v-toolbar>
        <v-btn icon @click="close">
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>{{ track ? 'Edit' : 'Add' }} Track</v-toolbar-title>
        <v-spacer></v-spacer>
        <div class="toolbar__actions">
          <v-btn v-if="track" color="error" text @click="confirmDelete">Delete</v-btn>
          <v-btn text @click="close">Cancel</v-btn>
          <v-btn color="primary" @click="submit">Save</v-btn>
        </div>
      </v-toolbar>
      <v-card-text class="py-12 dialog__content">
        <v-text-field
          outlined
          v-model="form.title"
          label="Name"
          required
        ></v-text-field>
        <div class="file-input" @drop.prevent="addFile" @dragover.prevent>
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
        </div>
        <v-textarea
          v-if="false"
          outlined
          label="Lyrics"
          v-model="form.lyrics"
          required
        ></v-textarea>
        <edit-lyrics v-model="form.lyrics"></edit-lyrics>
      </v-card-text>
      <v-card-actions>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Client from '@/services/client';
import {
  Component, Prop, Watch, Vue,
} from 'vue-property-decorator';
import EditLyrics from '@/components/edit/EditLyrics.vue';

interface Form {
  title: string|null;
  lyrics: Array<any>|null;
  audio: string|Blob|null;
}
const defaults: Form = {
  title: null,
  lyrics: null,
  audio: null,
};

@Component({
  components: { EditLyrics },
})
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
    const file = e.dataTransfer.files[0];
    if (file.type.match(/audio.*/)) {
      // eslint-disable-next-line prefer-destructuring
      this.form.audio = file;
    }
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
        lyrics: lyrics ? JSON.parse(lyrics.content) : null,
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
    let response = await Client.post(
      `/v1/reciters/${reciterId}/albums/${albumId}/tracks?include=${this.includes}`,
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
    let response = await Client.patch(
      `/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}?include=${this.includes}`,
      data,
    );
    response = await this.uploadAudio(reciterId, albumId, id) || response;
    this.redirect(response);
  }
  async uploadAudio(reciterId, albumId, trackId) {
    if (this.form.audio) {
      const upload = new FormData();
      upload.append('audio', this.form.audio);
      return Client.post(
        `/v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}/media/audio`
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
      await Client.delete(
        `/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}`,
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

<style lang="scss" scoped>
.dialog__content {
  max-width: 800px;
  margin: 0 auto;
}
</style>
