<template>
  <div>
    <div class="form-group">
      <label class="col-md-3 control-label">Picture</label>
      <div class="col-md-6">
        <input type="file" class="form-control" @change="fileSelected">
        <div v-if="image">
          <img :src="image" width="300px" class="img">
          <button type="button" class="btn btn-danger" @click="removeImage"><i class="fa fa-fw fa-trash"></i></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      image: null,
    };
  },
  methods: {
    fileSelected(e) {
      const files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      this.createImage(files[0]);
    },

    createImage(file) {
      const reader = new FileReader();

      reader.onload = (e) => {
        this.image = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    removeImage() {
      this.image = '';
    },
  },
};
</script>`
