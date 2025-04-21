<style>
.h-30 {
  min-height: 30vh !important;
}
</style>
<template>
  <BModal
    v-model="modal"
    hide-footer
    title="Modal Resign"
    class="v-modal-custom"
    size="md"
  >
    <div>
      <form method="post" @submit.prevent="onSubmit">
        <div class="mb-1">
          <label for="terbit" class="form-label"> Resign</label>
          <flat-pickr
            v-model="form.tgl_resign"
            placeholder="Pilih Tanggal Resign"
            :config="{
              wrap: true, // set wrap to true only when using 'input-group'
              dateFormat: 'd-m-Y',
            }"
            class="form-control"
          ></flat-pickr>
        </div>
        <div class="mb-1 text-end">
          <!-- Buttons with Label -->
          <button
            type="submit"
            class="btn btn-primary btn-label waves-effect waves-light"
            :disabled="progress"
          >
            <i class="ri-save-line label-icon align-middle fs-16 me-2"></i>
            {{ progress ? "Tunggu dulu" : "Simpan" }}
          </button>
        </div>
      </form>
    </div>
  </BModal>
</template>
<script>
import { mKaryawanService } from "@/services/MKaryawanService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";

const initForm = () => ({
  tgl_resign: "",
});

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      title: "Setting Jabatan",
      modal: false,
      form: initForm(),
      progress: false,
      nip: null,
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.nip = null;
      this.form = initForm();
      this.modal = false;
    },
    async onSubmit() {
      this.show();
      this.progress = true;
      const [err, resp] = await mKaryawanService.resign(this.nip, this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        this.progress = false;
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.progress = false;
      this.hide();
      this.hideModal();
      this.$emit("fetch");
    },
  },
};
</script>
