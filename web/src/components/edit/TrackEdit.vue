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
export default {
  mounted() {
    this.editedTrack.title = this.track.title;
    if (this.track.lyrics) {
      this.editedTrack.lyrics = this.track.lyrics.content;
    }
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
    submit() {
      console.log('submitted the form');
      this.dialog = false;
    },
    clear() {
      this.dialog = false;
    },
  },
};
</script>
