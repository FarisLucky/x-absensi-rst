<template>
  <div class="mb-1">
    <BRow class="g-2 mb-3">
      <BCol md="4">
        <label>Foto Profil</label>
        <div class="mb-1">
          <img
            v-if="form.photo !== null"
            :src="`${form.photo_url_cast}?time=${+new Date()}`"
            :key="imgInc"
            class="rounded avatar-xl img-thumbnail"
          />
          <img
            v-else
            src="@/assets/images/profil.jpg"
            class="rounded avatar-xl img-thumbnail"
          />
        </div>
        <div>
          <BButton
            type="submit"
            variant="primary"
            @click.prevent="
              showUploadPhoto({
                photo: form?.photo,
                nip: form.nip,
              })
            "
          >
            <i class="ri-exchange-line"></i>
            Ganti Profil
          </BButton>
        </div>
      </BCol>
    </BRow>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">Data Diri</h5>
        <BRow class="g-2 mb-1">
          <BCol md="6">
            <label for="unit" class="form-label">
              Unit
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.id_unit"
              :options="listUnit"
              placeholder="Pilih Unit"
              :reduce="(u) => u.id"
              label="nama"
            ></v-select>
          </BCol>
          <BCol cols="6" md="3">
            <label for="jabatan" class="form-label">
              Jabatan
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.jabatan"
              :options="['STAF', 'KEPALA', 'DIREKTUR', 'SUPER_ADMIN']"
              placeholder="Pilih Jabatan"
              label="nama"
            ></v-select>
          </BCol>
          <BCol md="3">
            <label for="nip" class="form-label">
              NIP
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nip"
              class="form-control"
              placeholder="Masukkan NIP..."
              v-model="form.nip"
            />
          </BCol>
          <BCol md="3">
            <label for="nik" class="form-label">
              NIK
              <span class="text-danger">*</span>
            </label>
            <input
              type="number"
              id="nik"
              class="form-control"
              placeholder="Masukkan NIK..."
              v-model="form.nik"
            />
          </BCol>
          <BCol md="3">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="Masukkan Nama..."
              v-model="form.nama"
            />
          </BCol>
          <BCol md="3">
            <label for="sex" class="form-label">
              Sex
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.sex"
              :options="[
                {
                  id: 'L',
                  val: 'LAKI-LAKI',
                },
                {
                  id: 'P',
                  val: 'PEREMPUAN',
                },
              ]"
              :reduce="(sex) => sex.id"
              label="val"
              placeholder="Pilih Jenis Kelamin"
            ></v-select>
          </BCol>
          <BCol md="3">
            <label for="tempat_lahir" class="form-label">
              Tempat Lahir
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="tempat_lahir"
              class="form-control"
              placeholder="Masukkan Tempat Lahir..."
              v-model="form.tempat_lahir"
            />
          </BCol>
          <BCol md="3">
            <label for="jenis" class="form-label">
              Tanggal Lahir
              <span class="text-danger">*</span>
            </label>
            <div class="d-flex">
              <flat-pickr
                v-model="form.tgl_lahir"
                placeholder="Pilih Tanggal Lahir"
                :config="{
                  altInput: true,
                  altFormat: 'd-m-Y',
                  wrap: true, // set wrap to true only when using 'input-group'
                  dateFormat: 'Y-m-d',
                }"
                class="form-control"
              ></flat-pickr>
              <div class="input-group-append d-inline">
                <button
                  class="btn btn-soft-secondary"
                  type="button"
                  title="Clear"
                  data-clear
                >
                  <i class="ri-close-line" />
                  <span aria-hidden="true" class="sr-only">Clear</span>
                </button>
              </div>
            </div>
          </BCol>
          <BCol md="3">
            <label for="pendidikan" class="form-label"> Pendidikan </label>
            <v-select
              v-model="form.pendidikan"
              :options="[
                'TIDAK SEKOLAH',
                'SD',
                'SMP',
                'SMA',
                'S1',
                'S2',
                'S3',
                'LAIN-LAIN',
              ]"
              placeholder="Pilih Pendidikan"
            ></v-select>
          </BCol>
          <BCol md="3">
            <label for="agama" class="form-label"> Agama </label>
            <v-select
              v-model="form.agama"
              :options="['ISLAM', 'KRISTEN', 'HINDU', 'BUDHA', 'KONGUCHU']"
              placeholder="Pilih Agama"
            ></v-select>
          </BCol>
          <BCol md="3">
            <label for="alamat" class="form-label"> Alamat </label>
            <input
              type="text"
              id="alamat"
              class="form-control"
              placeholder="Jl. Surabaya..."
              v-model="form.alamat"
            />
          </BCol>
          <BCol md="3">
            <div class="mb-1">
              <label class="form-label">
                Provinsi
                <span class="text-danger">*</span>
              </label>
              <v-select
                v-model="form.prov"
                :options="provList"
                :loading="provLoader"
                label="nama"
                @option:selected="getKota"
                :reduce="(w) => w.kode"
                placeholder="Pilih Provinsi"
              />
            </div>
          </BCol>
          <BCol md="3">
            <label class="form-label"> Kota/Kab </label>
            <v-select
              v-model="form.kab"
              :options="kotaList"
              :loading="kotaLoader"
              label="nama"
              :disabled="kotaDisabled"
              :reduce="(w) => w.kode"
              @option:selected="getKec"
              placeholder="Pilih Kabupaten"
            />
          </BCol>
          <BCol md="3">
            <label class="form-label"> Kecamatan </label>
            <v-select
              v-model="form.kec"
              :options="kecList"
              :loading="kecLoader"
              @option:selected="getDesa"
              label="nama"
              :disabled="kecDisabled"
              :reduce="(w) => w.kode"
              placeholder="Pilih Kecamatan"
            />
          </BCol>
          <BCol md="3">
            <label class="form-label"> Desa </label>
            <v-select
              v-model="form.desa"
              :options="desaList"
              :loading="desaLoader"
              label="nama"
              :disabled="desaDisabled"
              :reduce="(w) => w.kode"
              placeholder="Pilih Desa"
            />
          </BCol>
          <BCol cols="6" md="3">
            <label for="rt_rw" class="form-label"> RT/RW </label>
            <input
              type="text"
              id="rt_rw"
              class="form-control"
              placeholder="RT/RW"
              v-model="form.rt_rw"
            />
          </BCol>
          <BCol cols="6" md="3">
            <label for="kodepos" class="form-label"> Kode Pos </label>
            <input
              type="text"
              id="kodepos"
              class="form-control"
              placeholder="Kode Pos"
              v-model="form.kodepos"
            />
          </BCol>
          <BCol md="3">
            <label for="telp" class="form-label"> Telp </label>
            <input
              type="text"
              id="telp"
              class="form-control"
              placeholder="Masukkan No Telp..."
              v-model="form.telp"
            />
          </BCol>
          <BCol md="3">
            <label for="email" class="form-label"> Email </label>
            <input
              type="email"
              id="email"
              class="form-control"
              placeholder="Masukkan Email..."
              v-model="form.email"
            />
          </BCol>
          <BCol md="3">
            <label for="tgl_masuk" class="form-label"> Tanggal Masuk </label>
            <div class="d-flex">
              <flat-pickr
                v-model="form.tgl_masuk"
                placeholder="Pilih Tanggal Masuk"
                :config="{
                  altInput: true,
                  altFormat: 'd-m-Y',
                  wrap: true, // set wrap to true only when using 'input-group'
                  dateFormat: 'Y-m-d',
                }"
                class="form-control"
              ></flat-pickr>
              <div class="input-group-append d-inline">
                <button
                  class="btn btn-soft-secondary"
                  type="button"
                  title="Clear"
                  data-clear
                >
                  <i class="ri-close-line" />
                  <span aria-hidden="true" class="sr-only">Clear</span>
                </button>
              </div>
            </div>
          </BCol>
          <BCol md="3">
            <label for="tgl_resign" class="form-label">
              Tanggal <i>Resign</i>
            </label>
            <div class="d-flex">
              <flat-pickr
                v-model="form.tgl_resign"
                placeholder="Pilih Tanggal Resign"
                :config="{
                  altInput: true,
                  altFormat: 'd-m-Y',
                  wrap: true, // set wrap to true only when using 'input-group'
                  dateFormat: 'Y-m-d',
                }"
                class="form-control"
              ></flat-pickr>
              <div class="input-group-append d-inline">
                <button
                  class="btn btn-soft-secondary"
                  type="button"
                  title="Clear"
                  data-clear
                >
                  <i class="ri-close-line" />
                  <span aria-hidden="true" class="sr-only">Clear</span>
                </button>
              </div>
            </div>
          </BCol>
          <BCol md="3">
            <label for="status_kerja" class="form-label">
              Status Kerja
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.status_kerja"
              :options="['KONTRAK', 'TRAINING', 'TETAP']"
              placeholder="Pilih Status"
            ></v-select>
          </BCol>
          <BCol md="3">
            <label for="cuti" class="form-label"> Jumlah Cuti </label>
            <input
              type="number"
              id="cuti"
              class="form-control"
              placeholder="Cuti..."
              v-model="form.jml_cuti"
              max="12"
              min="1"
            />
          </BCol>
        </BRow>
      </div>
      <div v-if="isSuperAdmin" class="hstack gap-2 justify-content-end mt-3">
        <BButton type="submit" variant="primary" @click.prevent="onSubmit">
          <i class="ri-save-2-line me-1 align-bottom"></i>
          Simpan
        </BButton>
        <BButton type="reset" variant="outline-secondary" @click="resetForm">
          <i class="ri-refresh-fill me-1 align-bottom"></i>
          Reset
        </BButton>
      </div>
    </BForm>
    <profil-crop
      ref="profilCropRef"
      :imgKey="imgInc"
      @reloadImg="onReloadImg"
    />
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { mKaryawanService } from "@/services/MKaryawanService";
import { spinnerMethods, toastMethods, toastState } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";
import { http } from "@/config";
import { SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { webUrl } from "@/config/http";
import { defineAsyncComponent } from "vue";

const initForm = () => ({
  id: "",
  nip: "",
  nik: "",
  nama: "",
  sex: "",
  tempat_lahir: "",
  tgl_lahir: "",
  agama: "",
  pendidikan: "",
  alamat: "",
  prov: "",
  kab: "",
  kec: "",
  desa: "",
  rt_rw: "",
  kodepos: "",
  jabatan: "",
  id_unit: "",
  telp: "",
  email: "",
  jml_cuti: "",
  status_kerja: "",
  tgl_masuk: "",
  tgl_resign: "",
  photo: null,
  photo_url_cast: null,
});

export default {
  components: {
    flatPickr,
    ProfilCrop: defineAsyncComponent(() => import("./ProfilCrop.vue")),
  },
  setup() {
    return { v$: useVuelidate() };
  },
  watch: {
    "form.nama"(newVal) {
      this.form.nama = newVal?.toString().toUpperCase();
    },
  },
  data() {
    let toast = this;

    return {
      form: initForm(),
      submitted: false,
      timeConfig: {
        enableTime: false,
        dateFormat: "d M, Y",
      },
      listUnit: [],
      dataEdit: false,
      provLoader: false,
      provList: [],
      kotaLoader: false,
      kotaList: [],
      kotaDisabled: true,
      kecLoader: false,
      kecList: [],
      kecDisabled: true,
      desaLoader: false,
      desaList: [],
      desaDisabled: true,
      toast,
      formJabatan: 0,
      imgInc: 0,
    };
  },
  validations() {
    return {
      form: {
        nik: { required },
        nip: { required },
        nama: { required },
        sex: { required },
        telp: { required },
        tempat_lahir: { required },
        tgl_lahir: { required },
        pendidikan: { required },
      },
    };
  },
  computed: {
    ...toastState,
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  created() {
    this.getListUnit();
    this.showKaryawan(this.$route.params.nip);
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,

    async showKaryawan(nip) {
      const [err, resp] = await mKaryawanService.show(nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.setUpdateData(resp.data);
    },
    async getProvinsi() {
      let length = 2;
      let end = 8;
      let url = `/wilayah?length=${length}&end=${end}`;
      this.provLoader = true;
      await http
        .get(url)
        .then((response) => {
          this.provList = response.data?.data;
        })
        .finally(() => {
          this.provLoader = false;
        });
    },

    async getKota() {
      let length = 5;
      let end = 2;
      let prov = this.form.prov;
      let url = `/wilayah?length=${length}&kode=${prov}&end=${end}`;
      this.kotaLoader = true;
      await http
        .get(url)
        .then((response) => {
          this.kotaList = response.data?.data;
          this.kotaDisabled = false;
        })
        .finally(() => {
          this.kotaLoader = false;
        });
    },

    async getKec() {
      let length = 8;
      let end = 5;
      let kota = this.form.kab;
      let url = `/wilayah?length=${length}&kode=${kota}&end=${end}`;
      this.kecLoader = true;
      await http
        .get(url)
        .then((response) => {
          this.kecList = response.data?.data;
          this.kecDisabled = false;
        })
        .finally(() => {
          this.kecLoader = false;
        });
    },

    async getDesa() {
      let length = 13;
      let prov = this.form.kec;
      let end = 8;
      let url = `/wilayah?length=${length}&kode=${prov}&end=${end}`;
      this.desaLoader = true;
      await http
        .get(url)
        .then((response) => {
          this.desaList = response.data?.data;
          this.desaDisabled = false;
        })
        .finally(() => {
          this.desaLoader = false;
        });
    },
    async getListUnit() {
      this.isLoading = true;

      const [err, resp] = await mUnitService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.listUnit = resp.data;
      this.isLoading = false;
    },

    async onUpdate() {
      const [err] = await mKaryawanService.update(
        Object.assign({}, this.form, {
          detail: this.dokumens,
        }),
        this.form.id
      );
      if (err) {
        if (err.response && err.response.status === 422) {
          // Laravel validation errors
          console.log(err.response?.data?.errors);
          let error = Object.values(err.response?.data?.errors).map(
            (item) => `${item[0]}\n`
          )[0];
          this.toastError({
            title: "Gagal",
            msg: error,
          });
        } else {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
        }
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil diubah",
      });
      this.$router.push({ name: "KaryawanList" });
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
      this.onUpdate();
    },

    resetForm() {
      this.$data.form = initForm();
      this.getListUnit();
      this.showKaryawan(this.$route.params.nip);
    },

    setUpdateData(params) {
      this.form.id = params?.id;
      this.form.nip = params?.nip;
      this.form.nik = params?.nik;
      this.form.nama = params?.nama;
      this.form.sex = params?.sex;
      this.form.tempat_lahir = params?.tempat_lahir;
      this.form.tgl_lahir = params?.tgl_lahir_cast;
      this.form.agama = params?.agama;
      this.form.pendidikan = params?.pendidikan;
      this.form.pendidikan = params?.pendidikan;
      this.form.alamat = params?.alamat;
      this.form.prov = params?.prov;
      this.form.kab = params?.kab;
      this.form.kec = params?.kec;
      this.form.desa = params?.desa;
      this.form.rt_rw = `${params?.rt}/${params.rw}`;
      this.form.kodepos = params?.kodepos;
      this.form.jabatan = params?.jabatan;
      this.form.id_unit = params.id_unit == 0 ? "" : params?.id_unit;
      this.form.telp = params?.telp;
      this.form.email = params?.email;
      this.form.jml_cuti = params?.jml_cuti;
      this.form.status_kerja = params?.status_kerja;
      this.form.tgl_masuk = params?.tgl_masuk;
      this.form.tgl_resign = params?.tgl_resign;
      this.form.photo = params?.photo;
      this.form.photo_url_cast = params?.photo_url_cast;
      if (![this.form.prov, this.form.kab, this.form.kec].includes("")) {
        Promise.all([
          this.getProvinsi(),
          this.getKota(),
          this.getKec(),
          this.getDesa(),
        ]);
      }
      this.form.photo = params?.photo;
    },
    getListDokumens() {
      return [
        {
          jenis: "sip",
          nomor: "",
          terbit: "",
          akhir: "",
        },
        {
          jenis: "str",
          nomor: "",
          terbit: "",
          akhir: "",
        },
      ];
    },
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
    showUploadPhoto(row) {
      this.$refs.profilCropRef.modal = true;
      this.$refs.profilCropRef.user.photo = row.photo;
      this.$refs.profilCropRef.user.nip = row.nip;
    },
    onReloadImg() {
      this.imgInc++;
      console.log(this.imgInc);
    },
  },
};
</script>
