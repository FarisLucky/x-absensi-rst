<template>
  <BModal
    v-model="modal"
    hide-footer
    class="v-modal-custom"
    centered
    size="lg"
    header-class="p-3 bg-info-subtle"
    title="Detail Presensi"
    bodyClass="bg-light p-2"
    scrollable
  >
    <div class="p-1 overflow">
      <form @submit.prevent="onSubmit">
        <BCard no-body>
          <BCardBody class="border-bottom border-primary border-bottom-dashed">
            <h5 class="mb-1">Data Karyawan</h5>
            <BRow class="g-2 p-2">
              <BCol lg="5">
                <label>Nama</label>
                <v-select
                  v-model="form.karyawan.nama"
                  :options="karyawanList"
                  placeholder="Cari Karyawan"
                  :reduce="(k) => k.nama"
                  label="nama"
                  @search="onSearchKaryawan"
                  @option:selected="selectKaryawan"
                ></v-select>
                <small class="text-danger m-0">ketik minimal 3 karakter</small>
              </BCol>
              <BCol lg="3">
                <label>Nip</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.karyawan?.nip"
                  disabled
                />
              </BCol>
              <BCol lg="4">
                <label>Unit</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.karyawan?.m_unit?.nama"
                  disabled
                />
              </BCol>
            </BRow>
          </BCardBody>
          <BCardBody
            v-if="karyawanSt"
            class="border-bottom border-primary border-bottom-dashed"
          >
            <h5 class="mb-1">Data Jadwal</h5>
            <BRow class="g-2 p-2">
              <BCol cols="6" lg="3">
                <label>Tanggal</label>
                <v-select
                  :modelValue="form.jadwal.tanggal_cast"
                  :options="jadwalList"
                  placeholder="Cari Tanggal"
                  label="tanggal_cast"
                  @search="onSearchJadwal"
                  @update:modelValue="selectJadwal"
                ></v-select>
              </BCol>
              <BCol cols="6" lg="3">
                <label>Kode Shift</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.jadwal?.kode_shift"
                  disabled
                />
              </BCol>
              <BCol cols="6" lg="3">
                <label>Shift</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.jadwal?.shift"
                  disabled
                />
              </BCol>
              <BCol cols="6" lg="3">
                <label>Keterlambatan</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.jadwal?.telat_masuk + ' menit' ?? '-'"
                  disabled
                />
              </BCol>
              <BCol cols="6" lg="3">
                <label>Masuk</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.jadwal?.jam_masuk"
                  disabled
                />
              </BCol>
              <BCol cols="6" lg="3">
                <label>Pulang</label>
                <input
                  type="text"
                  class="form-control"
                  :value="form?.jadwal?.jam_pulang"
                  disabled
                />
              </BCol>
            </BRow>
          </BCardBody>
          <BCardBody
            v-if="jadwalSt"
            class="border-bottom border-primary border-bottom-dashed"
          >
            <h5 class="mb-2">Data Presensi</h5>
            <BRow class="g-2 p-2">
              <BCol cols="6" lg="3">
                <label>Jam Masuk</label>
                <flat-pickr
                  v-model="form.presensi.masuk"
                  :config="{
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                  }"
                  class="form-control"
                  required
                  placeholder="Jam Masuk"
                ></flat-pickr>
              </BCol>
              <BCol cols="6" lg="3">
                <label>Tgl Pulang</label>
                <flat-pickr
                  v-model="form.presensi.tgl_pulang"
                  :config="{
                    dateFormat: 'd-m-Y',
                  }"
                  class="form-control"
                  placeholder="Tgl Pulang"
                  required
                ></flat-pickr>
              </BCol>
              <BCol cols="6" lg="3">
                <label>Jam Pulang</label>
                <flat-pickr
                  v-model="form.presensi.pulang"
                  :config="{
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                  }"
                  class="form-control"
                  placeholder="Jam Pulang"
                  required
                ></flat-pickr>
              </BCol>
              <BCol cols="6" lg="3">
                <label>Status</label>
                <v-select
                  v-model="form.presensi.status"
                  :options="['TEPAT', 'TELAT']"
                  placeholder="Pilih Status"
                ></v-select>
              </BCol>
              <BCol cols="6" lg="3">
                <label>Device</label>
                <v-select
                  v-model="form.presensi.device"
                  :options="['Android', 'web']"
                  placeholder="Pilih Device"
                ></v-select>
              </BCol>
              <BCol lg="9">
                <label>Keterangan Terlambat</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.presensi.presensi_terlambat.ket"
                />
              </BCol>
              <BCol v-if="isSuperAdmin && jadwalSt">
                <div class="text-end">
                  <BButton type="submit" variant="primary">
                    <i class="ri-save-3-line"></i>
                    Simpan
                  </BButton>
                </div>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>
      </form>
    </div>
  </BModal>
</template>
<script>
import animationData4 from "@/components/widgets/pithnlch.json";
import { SUPER_ADMIN, TELAT, TEPAT } from "@/helpers/utils";
import { jadwalService } from "@/services/JadwalService";
import { mKaryawanService } from "@/services/MKaryawanService";
import { presensiService } from "@/services/PresensiService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";

const initForm = () => ({
  jadwal: {
    id: "",
  },
  karyawan: {
    nip: "",
    nama: "",
  },
  presensi: {
    id: "",
    masuk: "",
    tgl_pulang: "",
    pulang: "",
    status: "",
    device: "",
    presensi_terlambat: {
      ket: "",
    },
  },
});

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      animationData4,
      form: initForm(),
      modal: false,
      karyawanSt: false,
      jadwalSt: false,
      karyawanList: [],
      jadwalList: [],
      TELAT,
      TEPAT,
    };
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    hideModal() {
      this.modal = false;
      this.presensi = null;
    },
    showModal() {
      this.form = initForm();
      this.modal = true;
    },
    async onSearchKaryawan(search, loading) {
      if (search.length > 3) {
        loading(true);
        const [err, resp] = await mKaryawanService.data(search);
        if (err) {
          loading(false);
          this.karyawanSt = false;
          return;
        }
        this.karyawanList = resp.data;
        loading(false);
      }
    },
    async selectKaryawan(karyawan) {
      this.form.karyawan = karyawan;
      let date = new Date();
      await this.getJadwal({
        nip: karyawan.nip,
        month: date.getMonth() + 1,
        year: date.getFullYear(),
      });
      this.karyawanSt = true;
    },
    async getJadwal(nip) {
      this.show();
      const [err, resp] = await jadwalService.showByNip(nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.jadwalSt = false;
        this.hide();
        return;
      }
      this.jadwalList = resp.data;
      this.hide();
    },
    selectJadwal(jadwal) {
      if (jadwal.presensi !== null) {
        this.toastError({
          title: "Gagal",
          msg: "Presensi sudah ada, Cari Tanggal Lain !!!",
        });
        return;
      }
      this.form.jadwal = jadwal;
      this.jadwalSt = true;
    },
    async showPresensi() {
      this.show();
      const [err, resp] = await presensiService.show(this.form.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.presensi = resp.data;
      this.hide();
    },
    async onSubmit() {
      this.show();
      const [err] = await presensiService.store({
        nip: this.form.karyawan.nip,
        nama: this.form.karyawan.nama,
        id_jadwal: this.form.jadwal.id,
        presensi: {
          ...this.form.presensi,
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
      this.toastSuccess({
        title: "Berhasil",
        msg: "OK",
      });
      this.hide();
      this.hideModal();
    },
  },
};
</script>
<style></style>
