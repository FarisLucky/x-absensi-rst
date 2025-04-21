<template>
  <BModal
    v-model="modal"
    size="xl"
    title="Upload Foto Profil"
    class="v-modal-custom"
    hide-footer
  >
    <div class="p-2">
      <div class="mb-3">
        <div>
          <img
            v-if="user.photo !== null"
            :src="`${getProfil(user.nip)}?time=${+new Date()}`"
            :key="imgKey"
            class="rounded avatar-xl img-thumbnail"
          />
          <img
            v-else
            src="@/assets/images/profil.jpg"
            class="avatar-xl me-1 rounded material-shadow"
          />
          <div class="absolute bottom-0 left-0 right-0">
            <button class="btn btn-primary" @click="showCropper = true">
              Pilih Gambar
            </button>
          </div>
        </div>
        <h5 class="font-bold mb-0">{{ user.nama }}</h5>
        <div class="text-gray-500">{{ user.unit }}</div>
      </div>
      <div>
        <div class="mb-1">
          <avatar-cropper
            @completed="handleCompleted"
            @error="handlerError"
            @changed="changeFile"
            v-model="showCropper"
            :cropper-options="{ zoomable: true }"
            :labels="{ submit: 'Simpan', cancel: 'Batal' }"
            :upload-handler="handler"
            :request-options="{
              method: 'POST',
              headers: {
                Authorization: 'Bearer ' + token,
              },
            }"
          />
        </div>
      </div>
    </div>
  </BModal>
</template>
<script>
import { http, url } from "@/config";
import { token, webUrl } from "@/config/http";
import { toastMethods, toastState } from "@/state/helpers";
import Compressor from "compressorjs";
import "vue-advanced-cropper/dist/style.css";
import AvatarCropper from "vue-avatar-cropper";

export default {
  components: {
    AvatarCropper,
  },
  props: ["imgKey"],
  data() {
    return {
      id: null,
      modal: false,
      showCropper: false,
      user: {
        id: 1,
        nip: 0,
        nama: "",
        unit: "",
        photo: null,
      },
      apiUrl: url,
      token,
      filename: null,
    };
  },
  computed: {
    ...toastState,
  },
  methods: {
    ...toastMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
      this.id = null;
      this.photo = null;
    },
    changeFile(params) {
      this.filename = params.file.name;
    },
    dataURLtoFile(dataurl, filename) {
      var arr = dataurl.split(","),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);

      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }

      return new File([u8arr], filename, { type: mime });
    },
    async handler(cropper) {
      var imgdat = cropper.getCroppedCanvas().toDataURL(this.cropperOutputMime);
      var file = this.dataURLtoFile(imgdat, this.filename);
      let nip = this.user.nip;
      const success = async (result) => {
        let formData = new FormData();
        formData.append("photo", result, result.name);
        await http.post("profil/update-avatar/" + nip, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        //   this.modal = false;
        this.modal = false;
        this.$emit("reloadImg");
      };
      new Compressor(file, {
        quality: 0.6,

        // The compression process is asynchronous,
        // which means you have to access the `result` in the `success` hook function.
        success,
        error(err) {
          console.log(err.message);
        },
      });
    },
    handleCompleted() {
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil diubah",
      });
      this.modal = false;
      this.$emit("reloadImg");
    },
    handlerError() {
      this.toastError({
        title: "Gagal",
        msg: "Oops! Something went wrong...",
      });
    },
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
  },
};
</script>
