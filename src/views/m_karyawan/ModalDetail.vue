<style>
.h-30 {
  min-height: 30vh !important;
}
</style>
<template>
  <BModal
    v-model="modal"
    hide-footer
    title="Detail Karyawan"
    class="v-modal-custom"
    size="xl"
  >
    <div>
      <form method="post" @submit.prevent="onSubmit">
        <div
          v-for="(data, idx) in form"
          :key="idx"
          class="mb-1 border rounded p-2 bg-body"
        >
          <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
            Data {{ idx.toUpperCase() }}
          </h5>
          <BRow class="g-2 align-items-end">
            <BCol lg="3">
              <label for="sip" class="form-label"> Nomor </label>
              <input
                type="text"
                class="form-control"
                placeholder="Masukkan Nomor..."
                v-model="data.nomor"
              />
            </BCol>
            <BCol lg="3">
              <label for="terbit" class="form-label"> Tanggal Terbit </label>
              <flat-pickr
                v-model="data.terbit"
                placeholder="Pilih Tanggal Terbit"
                :config="{
                  wrap: true, // set wrap to true only when using 'input-group'
                  dateFormat: 'Y-m-d',
                }"
                class="form-control"
              ></flat-pickr>
            </BCol>
            <BCol lg="3">
              <label for="akhir" class="form-label"> Tanggal Akhir </label>
              <flat-pickr
                v-model="data.akhir"
                placeholder="Pilih Tanggal Akhir"
                :config="{
                  wrap: true, // set wrap to true only when using 'input-group'
                  dateFormat: 'Y-m-d',
                }"
                class="form-control"
              ></flat-pickr>
            </BCol>
            <BCol v-show="data.id">
              <BButton
                @click.prevent="onRemoveDetail(data.id)"
                v-b-tooltip="`Kosongi ${idx.toUpperCase()}`"
                variant="soft-secondary"
              >
                <i class="ri ri-delete-bin-line"></i>
              </BButton>
            </BCol>
          </BRow>
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
  id: "",
  jenis: "",
  nip: "",
  nomor: "",
  terbit: "",
  akhir: "",
});

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      title: "Setting Jabatan",
      modal: false,
      form: {
        str: initForm(),
        sip: initForm(),
      },
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
      this.form = {
        str: initForm(),
        sip: initForm(),
      };
      this.modal = false;
    },
    async onShow() {
      this.show();
      const [err, resp] = await mKaryawanService.detail(this.nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      if (resp.data.length > 0) {
        resp.data.forEach((detail) => {
          if (detail.jenis === "sip") {
            this.form.sip = detail;
          } else if (detail.jenis === "str") {
            this.form.str = detail;
          }
        });
      }
      this.hide();
    },
    async onSubmit() {
      this.show();
      this.progress = true;
      this.form.nip = this.nip;
      const [err, resp] = await mKaryawanService.storeDetail(this.form);
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
    },
    async onRemoveDetail(id) {
      this.show();
      const [err, resp] = await mKaryawanService.removeDetail(id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.form = {
        str: initForm(),
        sip: initForm(),
      };
      this.hide();
      this.onShow();
    },
  },
};
</script>
