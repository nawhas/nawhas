<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on">Edit</v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="headline">Edit {{ album.title }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="editedAlbum.title"
                label="Title"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="editedAlbum.year"
                label="Year"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-file-input v-model="editedAlbum.artwork" accept="image/*" label="Artwork"></v-file-input>
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
      editedAlbum: {
        title: null,
        year: null,
        artwork: null,
      },
    };
  },
  props: ['album'],
  methods: {
    setDataFromProp() {
      this.editedAlbum.title = this.album.title;
      this.editedAlbum.year = this.album.year;
    },
    async submit() {
      const formData = {};
      if (this.album.title !== this.editedAlbum.title) {
        if (this.album.title) {
          formData.title = this.editedAlbum.title;
        }
      }
      if (this.album.year !== this.editedAlbum.year) {
        if (this.album.year) {
          formData.year = this.editedAlbum.year;
        }
      }
      await axios.patch(
        `${process.env.VUE_APP_API_DOMAIN}/v1/reciters/${this.album.reciterId}/albums/${this.album.id}`,
        formData,
      );

      if (this.editedAlbum.artwork) {
        const imageFormData = new FormData();
        imageFormData.append('artwork', this.editedAlbum.artwork);
        await axios.post(
          `${process.env.VUE_APP_API_DOMAIN}/v1/reciters/${this.album.reciterId}/albums/${this.album.id}/artwork`,
          imageFormData,
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
