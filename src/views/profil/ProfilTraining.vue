<template>
  <div>
    <vue-good-table
      mode="local"
      :columns="columns"
      :rows="rows"
      :pagination-options="{
        enabled: true,
      }"
      :sort-options="{
        enabled: false,
      }"
      :line-numbers="true"
      theme="polar-bear"
      styleClass="vgt-table striped"
    >
      <template #table-row="props">
        <span v-if="props.column.field === 'train.status'">
          <span
            v-if="props.row.train.status === 1"
            class="badge badge-gradient-success"
            >SELESAI</span
          >
          <span v-else class="badge badge-gradient-dark">BELUM</span>
        </span>
        <span v-if="props.column.field === 'aksi'">
          <div>
            <BButton
              v-if="props.row.cin === null && props.row.train.status === 0"
              @click.prevent="scanBarcode({ id: props.row.id, jenis: 'cin' })"
              variant="soft-warning"
              size="sm"
            >
              <i class="ri-qr-scan-line"></i>
            </BButton>
          </div>
        </span>
      </template>
    </vue-good-table>

    <BModal
      v-model="modal"
      hide-footer
      title="Scan Untuk Masuk"
      class="v-modal-custom"
      size="lg"
    >
      <qrcode-stream
        v-if="modal"
        :constraints="{ facingMode }"
        @detect="onDetect"
        @camera-on="onReady"
        style="width: 100%"
      >
        <button @click="switchCamera" class="btn btn-soft-info">
          <i class="ri-camera-switch-line fs-24"></i>
        </button>
      </qrcode-stream>
    </BModal>
  </div>
</template>

<script>
import { http } from "@/config";
import { profileService } from "@/services/ProfileService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { QrcodeStream } from "vue-qrcode-reader";

export default {
  components: {
    VueGoodTable,
    QrcodeStream,
  },
  data() {
    return {
      columns: [
        {
          label: "Judul",
          field: "train.judul",
        },
        {
          label: "Jenis",
          field: "train.jenis",
        },
        {
          label: "Tanggal",
          field: "train.mulai_cast",
        },
        {
          label: "Jam",
          field: "train.jam",
        },
        {
          label: "Tempat",
          field: "train.tempat",
        },
        {
          label: "Pemateri",
          field: "train.pemateri",
        },
        {
          label: "Poin",
          field: "train.poin",
        },
        {
          label: "Status",
          field: "train.status",
        },
        {
          label: "Aksi",
          field: "aksi",
        },
      ],
      rows: [],
      decodedText: "",
      facingMode: "environment",
      modal: false,
      form: {
        id: "",
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async fetchData() {
      this.show();
      const [err, resp] = await profileService.getPelatihan(
        this.$route.params.nip
      );
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.rows = resp.data;
      this.hide();
    },
    scanBarcode({ id, jenis }) {
      this.modal = true;
      this.form.id = id;
      this.form.jenis = jenis;
    },
    hideModal() {
      this.modal = false;
    },
    switchCamera() {
      switch (this.facingMode) {
        case "environment":
          this.facingMode = "user";
          break;
        case "user":
          this.facingMode = "environment";
          break;
      }
    },
    async onDetect(detected) {
      let link = detected[0].rawValue;

      try {
        this.show;

        await http.post(link, { id: this.form.id });

        this.toastSuccess({
          title: "Berhasil",
          msg: "Check In",
        });

        this.hide();
        this.hideModal();
        this.fetchData();
      } catch (error) {
        this.toastError({
          title: "Gagal",
          msg: error.response?.data?.errors,
        });
        console.log(error);
        this.hide();
      }
    },
    onReady(capability) {
      console.log(capability);
    },
  },
};
</script>
