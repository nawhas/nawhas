<template>
  <div>
    <v-app-bar fixed elevate-on-scroll>
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
    </v-app-bar>
    <v-sheet>
      <v-container class="app__section">
        <v-text-field outlined v-model="form.title" label="Name" required></v-text-field>
        <div class="file-input" @drop.prevent="addFile" @dragover.prevent>
            <v-file-input
            v-model="form.audio"
            label="Audio File"
            placeholder="Upload Track Audio File"
            prepend-icon="volume_up"
            outlined
            accept="audio/*"
            :show-size="1000"
            >
            <template v-slot:selection="{ text }">
                <v-chip color="deep-orange accent-4" dark label small>{{ text }}</v-chip>
            </template>
            </v-file-input>
        </div>
        <v-textarea v-if="false" outlined label="Lyrics" v-model="form.lyrics" required></v-textarea>
        <timestamped-editor v-model="form.lyrics"></timestamped-editor>
      </v-container>
    </v-sheet>
  </div>
</template>

<script lang="ts">
import Client from '@/services/client';
import {
  Component, Prop, Vue,
} from 'vue-property-decorator';
import { goToTrack, goToReciter } from '@/router/helpers';
import TimestampedEditor from '@/components/edit/lyrics/TimestampedEditor.vue';

interface Form {
  title: string | null;
  lyrics: Array<any> | null;
  audio: string | Blob | null;
}
const defaults: Form = {
  title: null,
  lyrics: null,
  audio: null,
};

@Component({
  components: { TimestampedEditor },
})
export default class EditTrack extends Vue {
  @Prop({ type: Object }) private trackObject;
  @Prop({ type: Object }) private albumObject;
  private form: Form = { ...defaults };
  private loading = false;

  get track() {
    return this.trackObject;
  }

  get album() {
    return this.albumObject;
  }

  get includes() {
    return 'reciter,lyrics,album.tracks,media';
  }

  mounted() {
    this.resetForm();
  }

  get isJson() {
    try {
      JSON.parse(this.track.lyrics.content);
    } catch (e) {
      return false;
    }
    return true;
  }

  get lyrics() {
    const lyricsContent = this.track.lyrics.content;
    if (this.isJson) {
      return JSON.parse(lyricsContent);
    }
    const lyricsArray = lyricsContent.split(/\n/gi);
    const lyrics: object[] = [];
    for (let index = 0; index < lyricsArray.length; index++) {
      const text = lyricsArray[index];
      const group = {
        timestamp: null,
        lines: [{ text, repeat: 0 }],
      };
      lyrics.push(group);
    }
    return lyrics;
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
      const { title } = this.track;
      this.form = {
        ...this.form,
        title,
        lyrics: this.lyrics,
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
      data.lyrics = JSON.stringify(this.form.lyrics);
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
    const lyricsString = JSON.stringify(this.form.lyrics);
    if (this.track.lyrics?.content !== lyricsString && this.form.lyrics) {
      data.lyrics = lyricsString;
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
    goToTrack(
      response.data.reciter.slug,
      response.data.year,
      response.data.slug,
      { trackObject: response.data },
    ).catch(() => window.location.reload());
  }
  async confirmDelete() {
    // eslint-disable-next-line no-alert
    if (window.confirm(`Are you sure you want to delete '${this.track.title}'?`)) {
      const { id, reciterId, albumId } = this.track;
      await Client.delete(
        `/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}`,
      );
      goToReciter(this.track.reciter.slug);
    }
  }
  close() {
    this.loading = false;
    goToTrack(
      this.track.reciter.slug,
      this.track.album.year,
      this.track.slug,
      { trackObject: this.track },
    ).catch(() => window.location.reload());
  }
}
</script>

<style lang="scss" scoped>
.app__section {
  padding: 36px 12px !important;
  max-width: 816px;
  margin: 0 auto;
}
</style>
