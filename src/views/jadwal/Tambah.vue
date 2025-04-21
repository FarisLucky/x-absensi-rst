<template>
  <div>
    <BRow class="g-2">
      <BCol :lg="4">
        <BCard no-body>
          <BCardBody class="border-0">
            <div class="mb-1">
              <h5
                class="fs-14 mb-2 d-inline-block border-bottom pb-1 d-inline-block"
              >
                Pengaturan
              </h5>
              <div class="mb-1">
                <div>
                  <BFormRadioGroup
                    id="radio-slots"
                    v-model="form.type_tanggal"
                    :options="[
                      {
                        text: 'Harian',
                        value: 'harian',
                      },
                      {
                        text: 'Range',
                        value: 'range',
                      },
                    ]"
                  >
                  </BFormRadioGroup>
                </div>
              </div>
              <div class="mb-1">
                <label for="tanggal" class="form-label">
                  {{ !tanggalConf ? "Range" : "" }} Tanggal
                  <span class="text-danger">*</span>
                </label>
                <flat-pickr
                  v-if="tanggalConf"
                  v-model="form.tanggal"
                  placeholder="Pilih Tanggal"
                  :config="{
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                  }"
                  class="form-control bg-light border-light"
                ></flat-pickr>
                <flat-pickr
                  v-else
                  v-model="form.tanggal"
                  placeholder="Pilih Range Tanggal"
                  :config="{
                    defaultDate: 'Date',
                    mode: 'range',
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    disabled: [isSunday()],
                    locale: {
                      firstDayOfWeek: 1,
                    },
                  }"
                  class="form-control bg-light border-light"
                ></flat-pickr>
              </div>
              <div class="mb-1">
                <label for="kode_shift" class="form-label">
                  Shift
                  <span class="text-danger">*</span>
                </label>
                <v-select
                  v-model="form.kode_shift"
                  :options="listShift"
                  :reduce="(st) => st.kode"
                  label="nama"
                  placeholder="Pilih Shift (Masuk - Pulang)"
                ></v-select>
              </div>
              <div class="mb-1">
                <label for="jenis" class="form-label"> Jadwal Untuk </label>
                <v-select
                  v-model="jenisJadwal"
                  :options="['KARYAWAN', 'DIRI SENDIRI']"
                  placeholder="Pilih Presensi Pada"
                  class="mb-1"
                ></v-select>
              </div>
              <div v-if="jenisJadwal === 'KARYAWAN'" class="mb-1">
                <label for="nip" class="form-label">
                  Pilih Karyawan
                  <span class="text-danger">*</span>
                </label>
                <v-select
                  v-model="form.nip"
                  :options="listKaryawan"
                  :reduce="(st) => st.nip"
                  @search="onSearchKaryawan"
                  label="nama"
                  placeholder="Pilih Karyawan"
                ></v-select>
              </div>
              <div class="mb-1">
                <BButton
                  type="button"
                  variant="primary"
                  @click.prevent="setSetting"
                  class="me-1"
                >
                  <i class="ri-save-2-line me-1 align-bottom"></i>
                  Terapkan
                </BButton>
                <BButton
                  type="reset"
                  variant="outline-secondary"
                  @click="resetForm"
                >
                  <i class="ri-refresh-fill me-1 align-bottom"></i>
                  Reset
                </BButton>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol>
        <BCard no-body>
          <BCardBody class="border-0 h-100">
            <div class="d-flex justify-content-between mb-2">
              <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
                Data Jadwal
              </h5>
              <BButton
                variant="outline-secondary"
                @click.prevent="() => $router.back()"
              >
                <i class="ri-arrow-left-fill me-1"></i>
                Kembali
              </BButton>
            </div>
            <div class="mb-1">
              <BRow class="g-2">
                <BCol :lg="4">
                  <input
                    type="number"
                    id="nip"
                    class="form-control"
                    :value="karyawan?.nip"
                    disabled
                  />
                </BCol>
                <BCol :lg="4">
                  <input
                    type="text"
                    id="nama"
                    class="form-control"
                    :value="karyawan?.nama"
                    disabled
                  />
                </BCol>
              </BRow>
            </div>
            <div class="mb-1">
              <table class="table table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th style="width: 10%">No</th>
                    <th style="width: 20%">Tanggal</th>
                    <th>Shift</th>
                    <th>Libur</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(jadwal, idx) in jadwals" :key="idx">
                    <td>{{ ++idx }}</td>
                    <td>{{ jadwal.tanggal }}</td>
                    <td>
                      <v-select
                        v-model="jadwal.kode_shift"
                        :options="listShift"
                        :reduce="(st) => st.kode"
                        label="nama"
                        placeholder="Pilih Shift (Masuk - Pulang)"
                        :disabled="jadwal.libur"
                      ></v-select>
                    </td>
                    <td>
                      <BFormCheckbox
                        v-model="jadwal.libur"
                        value="1"
                        unchecked-value="0"
                        class="me-1"
                      >
                        Libur
                        {{ isSunday(jadwal.tanggal) }}
                      </BFormCheckbox>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mb-1 text-end">
              <BButton type="button" variant="primary" @click="onSubmit">
                <i class="ri-save-2-line me-1 align-bottom"></i>
                Simpan
              </BButton>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </div>
</template>
<script>
import { authState, toastMethods, toastState } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";
import { mShiftService } from "@/services/MShiftService";
import { mKaryawanService } from "@/services/MKaryawanService";
import { jadwalService } from "@/services/JadwalService";
import useVuelidate from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import dayjs from "dayjs";

const initForm = () => ({
  id: "",
  nip: "",
  tanggal: "",
  kode_shift: "",
  type_tanggal: "harian",
});

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      form: initForm(),
      listShift: [],
      listKaryawan: [],
      flatConf: {
        minDate: "today",
        dateFormat: "Y-m-d",
      },
      jadwals: [],
      karyawan: null,
      jenisJadwal: "KARYAWAN",
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    return {
      form: {
        // nip: {
        //     required: function () {
        //         return this.jenisJadwal === "KARYAWAN";
        //     },
        // },
        tanggal: { required },
        kode_shift: { required },
      },
    };
  },
  computed: {
    ...toastState,
    ...authState,
    tanggalConf() {
      return this.form.type_tanggal === "harian";
    },
  },
  created() {
    this.getShift();
    console.log(this.$store.state.auth.data);
  },
  methods: {
    ...toastMethods,

    async onSearchKaryawan(search, loading) {
      if (search.length > 3) {
        loading(true);

        const [err, resp] = await mKaryawanService.data(search);
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          loading(false);
          return;
        }
        this.listKaryawan = resp.data.map((k) => ({
          nip: k.nip,
          nama: k.nama,
        }));
        loading(false);
      }
    },

    async getShift() {
      const [err, resp] = await mShiftService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.listShift = resp.data;
    },

    async onSubmit() {
      const [err, resp] = await jadwalService.storeAll({
        jadwals: this.jadwals,
        karyawan: this.karyawan,
        jenisJadwal: this.jenisJadwal,
      });
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.resetForm();
      this.$router.push({
        name: "JadwalEdit",
        params: { nip: this.karyawan?.nip },
      });
    },

    resetForm() {
      this.$data.form = initForm();
      this.getShift();
    },
    async setSetting() {
      const result = await this.v$.$validate();
      if (!result) {
        this.toastError({
          title: "Gagal",
          msg: "Form wajib diisi",
        });
        return;
      }
      let tglSplit = this.form.tanggal;
      if (!this.tanggalConf) {
        tglSplit = this.form.tanggal.split("to");
      }
      if (this.tanggalConf) {
        tglSplit = [this.form.tanggal, this.form.tanggal];
      }
      let start = new Date(tglSplit[0]);
      let end = new Date(tglSplit[1]);
      this.resetJadwal();
      /**
       * split Jadwal harian atau range
       */
      if (tglSplit[1] === 0) {
        this.jadwals.push({
          tanggal: this.formatDate(start),
          kode_shift: this.form.kode_shift,
        });
        return;
      }
      for (var day = start; day <= end; day.setDate(day.getDate() + 1)) {
        // your day is here
        let date = this.formatDate(day);
        this.jadwals.push({
          tanggal: date,
          kode_shift: this.form.kode_shift,
          libur: this.isSunday(day),
        });
      }

      /**
       * jadwal untuk staff atau pribadi
       */
      if (this.jenisJadwal === "KARYAWAN") {
        this.karyawan = this.listKaryawan.filter(
          (karyawan) => karyawan.nip === this.form.nip
        )[0];
      } else {
        this.karyawan = {
          nip: this.data?.nip,
          nama: this.data?.nama,
        };
      }
    },
    resetJadwal() {
      this.jadwals = [];
    },
    formatDate(date) {
      var d = date,
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [day, month, year].join("-");
    },
    isSunday(date) {
      return dayjs(date).get('day') === 0 || dayjs(date).get('day') === 6;
    },
  },
};
</script>
