<template>
  <BModal
    v-model="modal"
    id="masukForm"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    title="Foto Absen"
  >
    <div>
      <form action="" @submit.prevent="onSubmit">
        <div v-if="cameraDenied !== ''" class="mb-1">
          <h5 class="fs-16">{{ cameraDenied }}</h5>
        </div>
        <div v-else>
          <div v-if="photo == null" class="mb-1">
            <WebCamUI
              v-if="modal"
              :fullscreenState="false"
              :fullscreenButton="{
                display: false,
              }"
              @photoTaken="photoTaken"
            />
          </div>
          <div v-else class="mb-1">
            <div v-if="photoFixed == null">
              <cropper
                class="cropper"
                :src="photo"
                :stencil-props="{
                  aspectRatio: 10 / 12,
                }"
                ref="cropper"
              />
            </div>
            <img v-else :src="photoFixed" style="max-width: 100%" />
            <BButton
              v-if="photoFixed == null"
              variant="success"
              @click.prevent="onCrop"
            >
              crop
            </BButton>
            <BButton
              variant="secondary"
              @click.prevent="
                () => {
                  photo = null;
                  photoFixed = null;
                }
              "
            >
              Reset
            </BButton>
          </div>
          <div v-if="photoFixed != null" class="d-grid gap-2 text-end pb-3">
            <BButton type="submit" variant="primary" :disabled="progress">
              {{ progress ? "Tunggu Dulu" : "Simpan" }}
            </BButton>
          </div>
        </div>
      </form>
    </div>
  </BModal>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { WebCamUI } from "vue-camera-lib";
import { Cropper } from "vue-advanced-cropper";
import "vue-advanced-cropper/dist/style.css";
import { lemburService } from "@/services/LemburService";

export default {
  components: {
    WebCamUI,
    Cropper,
  },
  data() {
    const user = this.$store.state.auth.data;
    return {
      id: null,
      modal: false,
      progress: false,
      user,
      cameraDenied: "",
      photo: null,
      photoFixed: null,
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
      this.requestCam();
    },
    hideModal() {
      this.modal = false;
      this.id = null;
      this.photo = null;
      this.photoFixed = null;
    },
    requestCam() {
      navigator.permissions
        .query({ name: "camera" })
        .then((permissionObj) => {
          console.log(permissionObj.state);
          if (permissionObj.state == "denied") {
            this.cameraDenied = "Silahkan Hidupkan Kamera lewat Chrome";
          } else if (permissionObj.state == "granted") {
            this.cameraDenied = "";
          }
        })
        .catch((error) => {
          console.log("Got error :", error);
        });
    },
    async onSubmit() {
      this.show();

      let formData = new FormData();
      formData.append("id", this.id);
      formData.append("photo", this.photo, "photo.jpeg");
      const [err, resp] = await lemburService.absenPhoto(formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.hide();
      this.hideModal();
      this.$emit("fetch");
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
    },
    photoTaken(data) {
      this.photo = data.image_data_url;
    },
    onCrop() {
      const { coordinates, canvas } = this.$refs.cropper.getResult();
      console.log(coordinates);
      canvas.toBlob((blob) => {
        this.photo = blob;
      }, "image/jpeg");
      this.photoFixed = canvas.toDataURL();
    },
  },
};
</script>
