<template>
  <BModal
    v-model="modal"
    id="mTolakForm"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    title="Tolak Izin"
  >
    <div>
      <form action="" @submit.prevent="onSubmit">
        <div class="mb-1">
          <p class="p-0 fs-11">
            <strong class="text-danger">*</strong>
            Masukkan Keterangan Penolakan
          </p>
        </div>
        <div class="mb-1">
          <label class="mb-1">
            Ket
            <span class="text-danger"></span>
          </label>
          <textarea
            type="textarea"
            class="form-control"
            v-model="form.ket"
          ></textarea>
        </div>
        <div class="d-grid gap-2 text-end pb-3">
          <BButton type="submit" variant="primary" :disabled="progress">
            {{ progress ? "Tunggu Dulu" : "Simpan" }}
          </BButton>
        </div>
      </form>
    </div>
  </BModal>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { izinService } from "@/services/IzinService";
import useVuelidate from "@vuelidate/core";
import { required } from "@vuelidate/validators";

const initForm = () => ({
  id: 0,
  acc: "",
  ket: "",
  jenis: "IZIN",
});

export default {
  data() {
    const user = this.$store.state.auth.data;
    return {
      modal: false,
      form: initForm(),
      progress: false,
      user,
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    return {
      form: {
        ket: { required },
      },
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
    },
    async onSubmit() {
      const result = await this.v$.$validate();
      if (!result) {
        this.toastError({
          title: "Gagal",
          msg: "Form wajib diisi",
        });
        return;
      }
      this.show();

      const [err, resp] = await izinService.tolak(this.form, this.form.id);
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
  },
};
</script>
