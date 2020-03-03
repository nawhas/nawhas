<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template v-slot:activator="{ on }">
      <v-btn dark text v-on="on">Edit</v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="headline">Edit {{ reciter.name }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="editedReciter.name"
                label="Name"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                label="Description"
                v-model="editedReciter.description"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <v-file-input v-model="editedReciter.avatar" accept="image/*" label="Avatar"></v-file-input>
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
    this.editedReciter.name = this.reciter.name;
    this.editedReciter.description = this.reciter.description;
  },
  data() {
    return {
      dialog: false,
      editedReciter: {
        name: null,
        description: null,
        avatar: null,
      },
    };
  },
  props: ['reciter'],
  methods: {
    submit() {
      console.log('submitted the form');
      this.dialog = false;
    },
    clear() {
      this.editedReciter = {
        name: this.reciter.name,
        description: this.reciter.description,
      };
      this.dialog = false;
    },
  },
};
</script>
