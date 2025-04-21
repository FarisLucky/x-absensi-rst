<template>
  <BModal
    v-model="modal"
    id="ket"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    size="xl"
    title="Detail Lembur"
  >
    <Simplebar style="max-height: 70vh" force-visible>
      <BRow class="g-2">
        <BCol cols="12">
          <h6
            class="m-0 badge bg-primary-subtle text-primary badge-border fs-14"
          >
            Data Karyawan
          </h6>
        </BCol>
        <BCol cols="6" md="2">
          <label>Nip</label>
          <input class="form-control" :value="lembur?.nip" readonly />
        </BCol>
        <BCol cols="6" md="3">
          <label>Nama</label>
          <input class="form-control" :value="lembur?.nama" readonly />
        </BCol>
        <BCol cols="6" md="3">
          <label>Unit</label>
          <input class="form-control" :value="lembur?.m_unit?.nama" readonly />
        </BCol>
        <BCol cols="12">
          <h6
            class="m-0 badge bg-primary-subtle text-primary badge-border fs-14"
          >
            Data Lembur
          </h6>
        </BCol>
        <BCol cols="6" md="2">
          <label>Pengajuan</label>
          <input class="form-control" :value="lembur?.created_at" readonly />
        </BCol>
        <BCol cols="6" md="2">
          <label>Tgl Lembur</label>
          <input class="form-control" :value="lembur?.tanggal_cast" readonly />
        </BCol>
        <BCol cols="6" md="2">
          <label>Waktu</label>
          <input
            class="form-control"
            :value="`${lembur?.mulai} - ${lembur?.akhir}`"
            readonly
          />
        </BCol>
        <BCol cols="6" md="2">
          <label>Total</label>
          <input class="form-control" :value="lembur?.ttl_jam_cast" readonly />
        </BCol>
        <BCol cols="6" md="2">
          <label>Jenis Absen</label>
          <input class="form-control" :value="lembur?.absen" readonly />
        </BCol>
        <BCol v-if="lembur?.absen == JENIS_ABSEN.GPS" cols="6" md="2">
          <label>Absen Masuk</label>
          <input class="form-control" :value="lembur?.masuk_cast" readonly />
        </BCol>
        <BCol v-if="lembur?.absen == JENIS_ABSEN.GPS" cols="6" md="2">
          <label>Absen Keluar</label>
          <input class="form-control" :value="lembur?.keluar_cast" readonly />
        </BCol>
        <BCol v-if="lembur?.absen == JENIS_ABSEN.FOTO" cols="6" md="2">
          <label>Absen FOTO</label>
          <div v-if="lembur?.bukti !== null">
            <BLink :href="lembur.bukti_url_cast" target="__blank"
              >Lihat Foto</BLink
            >
          </div>
          <input v-else value="TIDAK ABSEN" class="form-control" />
        </BCol>
        <BCol cols="12">
          <label>Keterangan</label>
          <input class="form-control" :value="lembur?.ket" readonly />
        </BCol>
        <BCol cols="12">
          <h6
            class="m-0 badge bg-primary-subtle text-primary badge-border fs-14"
          >
            Data ACC
          </h6>
        </BCol>
        <BCol cols="6" md="3">
          <label>ACC 1</label>
          <input class="form-control" :value="lembur?.acc1_by?.nama" readonly />
        </BCol>
        <BCol cols="6" md="2">
          <label>Tanggal ACC 1</label>
          <input class="form-control" :value="lembur?.acc1_at_cast" readonly />
        </BCol>
        <BCol cols="6" md="3">
          <label>ACC 2</label>
          <input class="form-control" :value="lembur?.acc2_by?.nama" readonly />
        </BCol>
        <BCol cols="6" md="2">
          <label>Tanggal ACC 2</label>
          <input class="form-control" :value="lembur?.acc2_at_cast" readonly />
        </BCol>
      </BRow>
    </Simplebar>
  </BModal>
</template>
<script>
import { JENIS_ABSEN } from "@/helpers/utils";
import Simplebar from "simplebar-vue";

export default {
  components: {
    Simplebar,
  },
  data() {
    return {
      modal: false,
      lembur: {},
      JENIS_ABSEN,
    };
  },
  methods: {
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
    },
  },
};
</script>
