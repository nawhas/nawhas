<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on">Edit</v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="headline">Edit {{ track.title }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="editedTrack.title"
                label="Title"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                v-if="track.lyrics"
                label="Lyrics"
                v-model="editedTrack.lyrics"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <v-file-input v-model="editedTrack.audio" accept="audio/*" label="Track"></v-file-input>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="warning" text @click="clear">Close</v-btn>
        <v-btn color="success" text @click="submit">Update</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
  mounted() {
    this.setDataFromProp();
  },
  data() {
    return {
      dialog: false,
      editedTrack: {
        title: null,
        lyrics: null,
        audio: null,
      },
    };
  },
  props: ['track'],
  methods: {
    setDataFromProp() {
      this.editedTrack.title = this.track.title;
      if (this.track.lyrics) {
        this.editedTrack.lyrics = this.track.lyrics.content;
      }
    },
    async submit() {
      const formData = {};
      if (this.track.title !== this.editedTrack.title) {
        if (this.editedTrack.title) {
          formData.title = this.editedTrack.title;
        }
      }
      if (this.track.lyrics) {
        if (this.track.lyrics.content !== this.editedTrack.lyrics) {
          if (this.editedTrack.lyrics) {
            formData.lyrics = this.editedTrack.lyrics;
          }
        }
      }
      const env = process.env.VUE_APP_API_DOMAIN;
      const { reciterId } = this.track;
      const { albumId } = this.track;
      const trackId = this.track.id;
      await axios.patch(
        `${env}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`,
        formData,
      );
      if (this.editedTrack.audio) {
        const audioFormData = new FormData();
        audioFormData.append('audio', this.editedTrack.audio);
        await axios.post(
          `${env}/v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}/media/audio`,
          audioFormData,
          { headers: { 'Content-Type': 'multipart/form-data' } },
        );
      }
      this.dialog = false;
      this.clear();
      window.location.reload();
    },
    clear() {
      this.setDataFromProp();
      this.dialog = false;
    },
  },
};
</script>
