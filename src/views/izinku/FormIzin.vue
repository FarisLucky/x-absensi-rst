<template>
  <BModal
    v-model="modal"
    id="izin"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    no-close-on-backdrop
    title="Izin"
    @close="hideModal"
  >
    <div>
      <form @submit.prevent="onSubmit" class="mb-4">
        <BRow class="g-2">
          <BCol cols="12">
            <div class="mb-1">
              <p class="p-0 fs-11 m-0 mb-2">
                <strong class="text-danger">*</strong>
                Data yang disajikan sesuai dengan aturan PT CKI
              </p>
            </div>
          </BCol>
          <BCol cols="12">
            <div class="d-flex">
              <img
                v-if="user?.photo !== null"
                :src="user?.photo_url_cast"
                class="avatar-sm me-1 rounded material-shadow"
              />
              <img
                v-else
                src="@/assets/images/profil.jpg"
                class="avatar-sm me-1 rounded material-shadow"
                width="30px"
              />
              <div class="p-2">
                <strong class="mb-1 d-block">{{ user?.nama }}</strong>
                <div class="badge badge-gradient-info">
                  {{ user.unit ?? "-" }}
                </div>
              </div>
            </div>
          </BCol>
          <BCol cols="12">
            <label class="mb-1">Jenis</label>
            <v-select
              v-model="form.izin"
              :options="listIzin"
              :reduce="(item) => item.kode"
              label="nama"
              placeholder="Pilih Jenis Izin"
              @option:selected="onSelectJenis"
            >
            </v-select>
          </BCol>
          <BCol cols="6">
            <label class="mb-1">
              Tanggal Mulai
              <span class="text-danger">*</span>
            </label>
            <flat-pickr
              v-model="form.mulai"
              placeholder="Pilih Tanggal"
              :config="{
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
              }"
              class="form-control bg-light border-light"
            ></flat-pickr>
          </BCol>
          <BCol cols="6">
            <label class="mb-1">
              Periode
              <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <input
                type="number"
                class="form-control"
                :value="form.periode"
                @input="onCheckPeriode($event.target.value)"
                min="1"
                :disabled="form.mulai === '' || form.mulai === null"
                placeholder="Tgl Mulai Diisi dulu"
              />
              <div class="input-group-append">
                <span class="input-group-text">Hari</span>
              </div>
            </div>
          </BCol>
          <BCol cols="6">
            <label class="mb-1">
              Tanggal Akhir
              <span class="text-danger me-1">*</span>
              <i
                class="ri-information-line"
                v-b-tooltip="'Tanggal Akhir Berdasarkan Jadwal Aktif'"
              ></i>
            </label>
            <flat-pickr
              v-model="form.akhir"
              placeholder="Pilih Tanggal"
              :config="{
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
              }"
              class="form-control bg-light border-light"
              disabled
            ></flat-pickr>
          </BCol>
          <BCol cols="6">
            <label class="mb-1">
              Tanggal Masuk
              <span class="text-danger me-1">*</span>
              <i
                class="ri-information-line"
                v-b-tooltip="'Tanggal Masuk Berdasarkan Jadwal Aktif'"
              ></i>
            </label>
            <flat-pickr
              v-model="form.masuk"
              placeholder="Pilih Tanggal"
              :config="{
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
              }"
              class="form-control bg-light border-light"
              disabled
            ></flat-pickr>
          </BCol>
          <BCol v-if="tahunan" md="6">
            <label class="mb-1">Cuti Akan Diambil</label>
            <div class="input-group">
              <input
                type="number"
                class="form-control"
                v-model="form.cuti_diambil"
                readonly
              />
              <div class="input-group-append">
                <span class="input-group-text">Hari</span>
              </div>
            </div>
          </BCol>
          <BCol v-if="tahunan" md="6">
            <label class="mb-1">Sisa</label>
            <div class="input-group">
              <input
                type="number"
                class="form-control"
                v-model="form.sisa"
                readonly
              />
              <div class="input-group-append">
                <span class="input-group-text">Hari</span>
              </div>
            </div>
          </BCol>
          <BCol cols="12">
            <label class="mb-1">Keterangan</label>
            <input type="text" class="form-control" v-model="form.ket" />
          </BCol>
          <BCol cols="12">
            <label class="mb-1">Upload</label>
            <FormUpload ref="fileUpload" />
          </BCol>
          <BCol cols="12">
            <p class="p-0 mb-1 fs-11">
              Wajib Diisi
              <strong class="text-danger">*</strong>
            </p>
          </BCol>
          <BCol cols="12">
            <div class="d-grid gap-2 text-end pb-3">
              <BButton type="submit" variant="primary" :disabled="progress">
                {{ progress ? "Tunggu Dulu" : "Simpan" }}
              </BButton>
            </div>
          </BCol>
        </BRow>
      </form>
    </div>
  </BModal>
</template>
<script>
import { webUrl } from "@/config/http";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { IPC, ICM, ICT } from "@/helpers/utils";
import useVuelidate from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { mIzinService } from "@/services/MIzinService";
import flatPickr from "vue-flatpickr-component";
import { izinService } from "@/services/IzinService";
import FormUpload from "@/components/form-upload.vue";
import { mKaryawanService } from "@/services/MKaryawanService";
import queryString from "query-string";
import { jadwalService } from "@/services/JadwalService";

const initForm = () => ({
  nip: "",
  jenis: "",
  izin: "",
  mulai: "",
  akhir: "",
  periode: "",
  masuk: "",
  ket: "",
  cuti_diambil: "",
  sisa: "",
});

export default {
  components: {
    flatPickr,
    FormUpload,
  },
  data() {
    const user = this.$store.state.auth.data;
    return {
      modal: false,
      form: initForm(),
      progress: false,
      webUrl,
      filter: {
        month: "",
        year: "",
      },
      user,
      listIzin: [],
      tahunan: 0,
      IPC,
      ICM,
      ICT,
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  created() {
    this.form.nip = this.user.nip;
  },
  validations() {
    return {
      form: {
        izin: { required },
        mulai: { required },
        periode: { required },
      },
    };
  },
  watch: {
    "form.periode"(newValue) {
      if (this.form.mulai !== "" || this.form.mulai !== null) {
        if (this.tahunan) {
          this.form.cuti_diambil = parseInt(newValue);
        }
      }
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
      this.getMasterIzin();
    },
    hideModal() {
      this.modal = false;
      this.resetForm();
    },
    async getMasterIzin() {
      this.show();

      const [err, resp] = await mIzinService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.listIzin = resp.data;
      this.hide();
    },
    async getSisaCuti() {
      this.show();

      const [err, resp] = await mKaryawanService.sisaCuti();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.form.sisa = resp.data;

      this.hide();
    },
    onSelectJenis(val) {
      // Reset Form
      this.form = Object.assign({}, initForm(), { izin: val.kode });
      this.form.nip = this.user.nip;
      this.tahunan = val.tahunan;
      if (this.tahunan) {
        this.getSisaCuti();
      }
    },
    resetForm() {
      this.form = initForm();
      this.tahunan = 0;
      this.$refs.fileUpload?.removeFiles();
    },
    async onCheckPeriode(periode) {
      if (
        this.form.mulai === "" ||
        this.form.mulai === null ||
        this.form.izin === ""
      ) {
        this.toastError({
          title: "Gagal",
          msg: "Jenis Izin atau Tanggal Mulai Kosong",
        });
        this.resetForm();
        return;
      }
      console.log(this.form.mulai);
      this.show();
      this.form.periode = periode;

      let query = queryString.stringify(
        {
          periode: this.form.periode,
          mulai: this.form.mulai,
          nip: this.form.nip,
          tahunan: this.tahunan,
        },
        {
          arrayFormat: "index",
        }
      );
      const [err, resp] = await jadwalService.checkPeriode(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.form.mulai = "";
        this.form.periode = "";
        this.hide();

        return;
      }
      this.form.akhir = resp.data.tgl_akhir;
      this.form.masuk = resp.data.tgl_masuk;
      if (this.tahunan) {
        this.form.sisa = resp.data.sisa_cuti;
      }
      this.hide();
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
      if (this.tahunan && (this.form.sisa === null || this.form.sisa < 1)) {
        this.toastError({
          title: "Gagal",
          msg: "Jumlah Cuti Kosong / Habis",
        });
        return;
      }
      this.show();

      let formData = new FormData();
      formData.append("izin", this.form.izin);
      formData.append("mulai", this.form.mulai);
      formData.append("akhir", this.form.akhir);
      formData.append("periode", this.form.periode);
      formData.append("masuk", this.form.masuk);
      formData.append("ket", this.form.ket);
      formData.append("cuti_diambil", this.form.cuti_diambil);
      formData.append("sisa", this.form.sisa);
      if (this.form.bukti && this.$refs.fileUpload?.myFiles[0] !== undefined) {
        formData.append("bukti", this.$refs.fileUpload?.myFiles[0]);
      }

      const [err, resp] = await izinService.store(formData, {
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
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.$emit("fetch");
    },
  },
};
</script>
