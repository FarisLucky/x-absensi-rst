<template>
  <simplebar id="scrollbar">
    <BRow class="g-2">
      <BCol md="6">
        <div class="p-2 rounded border">
          <h5>Data Diri</h5>
          <BRow class="g-2 px-2 py-1">
            <BCol cols="6">
              <label>NIP</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.nip"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Nama</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.nama"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>NIK</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.nik"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Tgl Lahir</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tgl_lahir_cast"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Tempat Lahir</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tempat_lahir"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Jenis Kelamin</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.sex"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Telp</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.telp"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Email</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.email"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Status Kerja</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.status_kerja"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Jml Anak</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.jml_anak"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Tanggal Masuk</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tgl_masuk"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Tanggal Akhir</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tgl_resign"
                disabled
              />
            </BCol>
          </BRow>
        </div>
      </BCol>
      <BCol md="6">
        <div class="p-2 rounded border mb-3">
          <h5>Data Alamat</h5>
          <BRow class="g-2 px-2 py-1">
            <BCol cols="8" md="8">
              <label>Alamat</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.alamat"
                disabled
              />
            </BCol>
            <BCol cols="2" md="2">
              <label>RT/RW</label>
              <input
                type="text"
                class="form-control"
                :value="`${karyawan?.rt}/${karyawan?.rw}`"
                disabled
              />
            </BCol>
            <BCol cols="2" md="2">
              <label>Kode Pos</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.kodepos"
                disabled
              />
            </BCol>
            <BCol cols="6" md="3">
              <label>Desa</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan.desa"
                disabled
              />
            </BCol>
            <BCol cols="6" md="3">
              <label>Kecamatan</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan.kec"
                disabled
              />
            </BCol>
            <BCol cols="6" md="3">
              <label>Kabupaten</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan.kab"
                disabled
              />
            </BCol>
            <BCol cols="6" md="3">
              <label>Provinsi</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan.prov"
                disabled
              />
            </BCol>
          </BRow>
        </div>
        <div class="p-2 rounded border">
          <h5>Data Pekerjaan</h5>
          <BRow class="g-2 px-2 py-1">
            <BCol cols="6">
              <label>Tanggal Masuk</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tgl_masuk"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Tanggal Resign</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.tgl_resign"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Unit</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.unit"
                disabled
              />
            </BCol>
            <BCol cols="6">
              <label>Jabatan</label>
              <input
                type="text"
                class="form-control"
                :value="karyawan?.jabatan"
                disabled
              />
            </BCol>
          </BRow>
        </div>
      </BCol>
    </BRow>
  </simplebar>
</template>

<script>
import { profileService } from "@/services/ProfileService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import simplebar from "simplebar-vue";

export default {
  components: {
    simplebar,
  },
  data() {
    return {
      karyawan: {},
    };
  },
  created() {
    this.onShow();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async onShow() {
      this.show();
      const [err, resp] = await profileService.show(this.$route.params.nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.karyawan = resp.data;
      this.hide();
    },
  },
};
</script>
