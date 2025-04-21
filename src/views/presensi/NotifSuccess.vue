<template>
  <BModal
    v-model="modal"
    hide-footer
    class="v-modal-custom"
    hide-header-close
    centered
    no-close-on-backdrop
  >
    <div class="modal-body text-center">
      <Lottie
        colors="primary:#121331,secondary:#08a88a"
        trigger="loop"
        :options="{
          animationData: animationData4,
        }"
        :height="120"
        :width="120"
      />
      <div class="mt-4">
        <h4 class="mb-3">
          Terima Kasih sudah Absen.
          {{ presensi?.status === TELAT ? "Anda Telat" : "" }}
        </h4>
        <div v-if="presensi?.status === TELAT" class="text-muted mb-4">
          <label>Keterangan Terlambat</label>
          <input
            type="text"
            class="form-control"
            v-model="form.ket"
            :required="presensi?.status === TELAT"
          />
        </div>
        <div class="hstack gap-2 justify-content-center">
          <BLink
            v-if="presensi?.status !== TELAT"
            href="javascript:void(0);"
            class="btn btn-link link-success fw-medium material-shadow-none"
            @click="hideModal"
          >
            <i class="ri-close-line me-1 align-middle"></i> Tutup
          </BLink>
          <BLink
            v-if="presensi?.status === TELAT"
            href="javascript:void(0);"
            class="btn btn-success"
            @click.prevent="submitKetTelat"
          >
            Simpan</BLink
          >
        </div>
      </div>
    </div>
  </BModal>
</template>
<script>
import Lottie from "@/components/widgets/lottie.vue";
import animationData4 from "@/components/widgets/pithnlch.json";
import { TELAT } from "@/helpers/utils";
import { presensiService } from "@/services/PresensiService";
import { spinnerMethods, toastMethods } from "@/state/helpers";

const initForm = () => ({
  ket: "",
});

export default {
  components: {
    Lottie,
  },
  data() {
    return {
      animationData4,
      form: initForm(),
      modal: false,
      presensi: null,
      TELAT,
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    hideModal() {
      this.modal = false;
      this.$router.go(0);
    },
    showModal() {
      this.modal = true;
    },
    async submitKetTelat() {
      const [err] = await presensiService.ketTelat(
        this.presensi?.presensi_terlambat?.id,
        this.form
      );
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "Keterangan Disimpan",
      });
      this.$router.go(0);
    },
  },
};
</script>
<style></style>
