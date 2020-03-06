<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark icon v-on="on"><v-icon>edit</v-icon></v-btn>
    </template>
    <v-card :loading="loading">
      <v-card-title>
        <span class="headline">Edit Track</span>
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
    const { title, lyrics } = this.track;
    this.form = {
      ...defaults,
      title,
      lyrics: lyrics.content,
    };
  }

  async submit() {
    this.loading = true;
    const { slug } = this.track;
    const data: any = {};
    if (this.track.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.track.lyrics.content !== this.form.lyrics && this.form.lyrics) {
      data.lyrics = this.form.lyrics;
    }

    const { id, reciterId, albumId } = this.track;
    const includes = 'reciter,lyrics,album.tracks,media';
    let response = await axios.patch(
      `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}?include=${includes}`,
      data,
    );

    if (this.form.audio) {
      const upload = new FormData();
      upload.append('audio', this.form.audio);
      response = await axios.post(
        `${API_DOMAIN}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${id}/media/audio?include=${includes}`,
        upload,
        { headers: { 'Content-Type': 'multipart/form-data' } },
      );
    }

    if (slug !== response.data.slug) {
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

    this.close();
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }
}
</script>
