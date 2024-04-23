<template>
  <v-dialog
    v-model="dialog"
    persistent
    fullscreen
    no-click-animation
    hide-overlay
    transition="dialog-bottom-transition"
  >
    <template #activator="{ on }">
      <v-btn
        v-if="track"
        dark
        icon
        v-on="on"
      >
        <v-icon>edit</v-icon>
      </v-btn>
      <v-btn
        v-else
        text
        v-on="on"
      >
        Add Track
      </v-btn>
    </template>
    <v-card :loading="loading">
      <v-app-bar
        fixed
        elevate-on-scroll
      >
        <v-btn
          icon
          @click="close"
        >
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>{{ track ? 'Edit' : 'Add' }} Track</v-toolbar-title>
        <v-spacer />
        <div class="toolbar__actions">
          <v-btn
            v-if="track"
            text
            @click="confirmDelete"
          >
            <v-icon>delete_forever</v-icon>
          </v-btn>
          <v-btn
            color="primary"
            @click="submit"
          >
            Save
          </v-btn>
        </div>
      </v-app-bar>
      <v-card-text class="dialog__content">
        <v-text-field
          v-model="form.title"
          outlined
          label="Name"
          required
        />
        <v-text-field
          v-model="form.video"
          outlined
          label="YouTube Video"
          prepend-icon="video_library"
        />
        <div
          class="file-input"
          @drop.prevent="addFile"
          @dragover.prevent
        >
          <v-file-input
            v-model="form.audio"
            label="Audio File"
            placeholder="Upload Track Audio File"
            prepend-icon="volume_up"
            outlined
            accept="audio/*"
            :show-size="1000"
          >
            <template #selection="{ text }">
              <v-chip
                color="deep-orange accent-4"
                dark
                label
                small
              >
                {{ text }}
              </v-chip>
            </template>
          </v-file-input>
        </div>
      </v-card-text>
      <v-card-actions />
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'nuxt-property-decorator';
import { RequestOptions, TrackIncludes } from '@/api/tracks';
import { getTrackUri } from '@/entities/track';
import { getReciterUri } from '@/entities/reciter';

interface Form {
  title: string|null;
  audio: File|null;
  video: string|null;
}
const defaults: Form = {
  title: null,
  audio: null,
  video: null,
};

@Component({})
export default class EditTrackDialog extends Vue {
  @Prop({ type: Object }) private track;
  @Prop({ type: Object }) private album;
  private dialog = false;
  private form: Form = { ...defaults };
  private loading = false;

  getRequestOptions(): RequestOptions {
    return {
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        TrackIncludes.Media,
        'album.tracks',
      ],
    };
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
      this.form.audio = file;
    }
  }

  removeFile() {
    this.form.audio = null;
  }

  resetForm() {
    this.form = { ...defaults };
    if (this.track) {
      const { title, video } = this.track;
      this.form = {
        ...this.form,
        title,
        video,
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
    if (this.form.video) {
      data.video = this.form.video;
    }
    const { reciterId } = this.album;
    const albumId = this.album.id;
    let response = await this.$api.tracks.store(
      reciterId,
      albumId,
      data,
      this.getRequestOptions(),
    );
    response = await this.uploadAudio(reciterId, albumId, response.id) || response;
    this.redirect(response);
  }

  async update() {
    const data: any = {};
    if (this.track.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.track.video !== this.form.video && this.form.video) {
      data.video = this.form.video;
    }
    const { id, reciterId, albumId } = this.track;
    let response = await this.$api.tracks.update(
      reciterId,
      albumId,
      id,
      data,
      this.getRequestOptions(),
    );
    if (this.form.audio) {
      response = await this.uploadAudio(reciterId, albumId, id) || response;
    }
    this.redirect(response);
  }

  async uploadAudio(reciterId, albumId, trackId) {
    if (!this.form.audio) {
      return false;
    }

    return await this.$api.tracks.changeAudio(
      reciterId,
      albumId,
      trackId,
      this.form.audio,
      this.getRequestOptions(),
    );
  }

  redirect(response) {
    this.$router.push(getTrackUri(response));
  }

  async confirmDelete() {
    if (window.confirm(`Are you sure you want to delete '${this.track.title}'?`)) {
      const { id, reciterId, albumId } = this.track;
      await this.$api.tracks.delete(reciterId, albumId, id);
      this.$router.push(getReciterUri(this.track.reciter));
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
  padding: 96px 12px !important;
}
</style>
