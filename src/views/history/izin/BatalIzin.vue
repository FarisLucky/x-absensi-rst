<template>
  <!-- Default Modals -->
  <BModal v-model="modal" hide-footer title="Batal Cuti" class="v-modal-custom">
    <form @submit.prevent="onSubmit">
      <div class="mb-1">
        <h5>{{ title }}</h5>
      </div>
      <div class="mb-1">
        <label>Alasan</label>
        <input type="text" class="form-control" v-model="form.ket" />
      </div>
      <div class="text-end">
        <BButton type="submit" variant="primary">
          <i class="ri-save-2-line"></i>
          Simpan
        </BButton>
      </div>
    </form>
  </BModal>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { izinService } from "@/services/IzinService";

export default {
  data() {
    return {
      modal: false,
      title: "",
      form: {
        id: "",
        ket: "",
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
      this.show();
      const [err, resp] = await izinService.batal(this.form.id, this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.tukar = resp.data;
      this.hide();
      this.hideModal();
      this.$emit("fetch");
    },
  },
};
</script>
