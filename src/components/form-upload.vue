<template>
  <div class="mb-1">
    <file-pond
      ref="pond"
      label-idle="Upload File Disini..."
      v-bind:allow-multiple="true"
      accepted-file-types="image/jpeg, image/png, application/pdf,"
      :instant-upload="false"
      v-bind:files="myFiles"
      @addfile="onAddfile"
      @removefile="onRemoveFile"
    />
  </div>
</template>

<script>
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview
);

export default {
  components: {
    FilePond,
  },
  data: function () {
    return {
      myFiles: [],
    };
  },
  methods: {
    onAddfile(err, file) {
      let fileInput = file.file;
      this.myFiles.push(fileInput);
    },
    onRemoveFile(err, file) {
      console.log(file);
      this.myFiles.splice(this.myFiles.indexOf(file.file), 1);
    },
    removeFile() {
      this.$refs.pond.removeFile();
    },
    removeFiles() {
      this.$refs.pond.files?.forEach((idx) => {
        this.$refs.pond.removeFile(idx);
      });
      this.myFiles = [];
    },
  },
};
</script>
