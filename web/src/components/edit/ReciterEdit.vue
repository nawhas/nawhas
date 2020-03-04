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
import axios from 'axios';

export default {
  mounted() {
    this.setDataFromProp();
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
    setDataFromProp() {
      this.editedReciter.name = this.reciter.name;
      this.editedReciter.description = this.reciter.description;
    },
    async submit() {
      let currentSlug = this.reciter.slug;

      const formData = {};
      if (this.reciter.name !== this.editedReciter.name) {
        if (this.editedReciter.name) {
          formData.name = this.editedReciter.name;
        }
      }
      if (this.reciter.description !== this.editedReciter.description) {
        if (this.editedReciter.description) {
          formData.description = this.editedReciter.description;
        }
      }

      const response = await axios.patch(
        `${process.env.VUE_APP_API_DOMAIN}/v1/reciters/${currentSlug}`,
        formData,
      );
      currentSlug = response.data.slug;

      if (this.editedReciter.avatar) {
        const imageFormData = new FormData();
        imageFormData.append('avatar', this.editedReciter.avatar);
        await axios.post(
          `${process.env.VUE_APP_API_DOMAIN}/v1/reciters/${currentSlug}/avatar`,
          imageFormData,
          { headers: { 'Content-Type': 'multipart/form-data' } },
        );
      }

      this.dialog = false;
      this.clear();
      this.$router.push({ name: 'reciters.show', params: { reciter: currentSlug } });
    },
    clear() {
      this.setDataFromProp();
      this.dialog = false;
    },
  },
};
</script>
