<style>
.data-karyawan {
  list-style-type: none;
  padding-left: 0;
}
.data-karyawan li span {
  display: inline-block;
  width: 5rem;
}
.data-karyawan li strong {
  padding: 0 0.3rem;
  margin-bottom: 4px;
  border-radius: 5px;
}
</style>
<template>
  <div>
    <BRow class="g-3">
      <BCol>
        <BCard no-body>
          <BCardBody class="border-top border-top-dashed border-primary">
            <div class="d-flex justify-content-between mb-1">
              <h5 class="fs-14 d-inline-block border-bottom pb-1">
                Data Jadwal
              </h5>
              <div>
                <BButton
                  variant="outline-secondary"
                  @click.prevent="() => $router.back()"
                >
                  <i class="ri-arrow-left-fill me-1"></i>
                  Kembali
                </BButton>
              </div>
            </div>
            <form>
              <BRow class="g-2 align-items-end mb-1">
                <div
                  v-if="isSuperAdmin || isKaSub || isKaBid || isDir"
                  class="col-6 col-md-4"
                >
                  <label for="tanggal" class="form-label">
                    Unit
                    <span class="text-danger">*</span>
                  </label>
                  <v-select
                    v-model="filter.id_unit"
                    :options="mUnits"
                    :reduce="(unit) => unit.id"
                    label="nama"
                    placeholder="Pilih Unit"
                  ></v-select>
                </div>
                <BCol cols="4" md="2">
                  <v-select
                    v-model="filter.year"
                    :options="years"
                    placeholder="Pilih Tahun"
                  ></v-select>
                </BCol>
                <BCol v-if="filter.year !== ''" cols="5" md="2">
                  <v-select
                    v-model="filter.month"
                    :options="monthsList"
                    :reduce="(l) => l.id"
                    label="name"
                    placeholder="Pilih Bulan"
                    @option:selected="fetchData"
                  ></v-select>
                </BCol>
                <div class="col-lg">
                  <BButton
                    type="submit"
                    variant="info"
                    @click.prevent="onGetJadwal"
                    class="me-1"
                    :disabled="progress"
                  >
                    <i class="ri-search-line me-1 align-bottom"></i>
                    {{ progress ? "Tunggu Dulu" : "Cari" }}
                  </BButton>
                  <BButton
                    type="reset"
                    variant="outline-secondary"
                    @click.prevent="onReset"
                    class="me-1"
                  >
                    <i class="ri-refresh-fill me-1 align-bottom"></i>
                  </BButton>
                </div>
                <div class="col-12">
                  <small>Silahkan isi filter diatas untuk menampilkan</small>
                </div>
              </BRow>
            </form>
            <div v-if="labels.length > 0" class="mb-1 table-responsive">
              <table
                class="table table-nowrap table-striped mb-1 table-bordered"
                style="height: 70vh"
              >
                <thead>
                  <tr>
                    <th style="width: 50%">Nama</th>
                    <th v-for="(label, idx) in labels" :key="idx">
                      {{ label }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, idx) in rows" :key="idx">
                    <td>
                      {{ idx }}
                      <BLink variant="primary"
                        >Jadwal Detail <i class="ri-play-line"></i
                      ></BLink>
                    </td>
                    <td
                      v-for="(jadwal, jIdx) in Object.entries(row)"
                      :key="jIdx"
                    >
                      <div
                        style="min-width: 160px"
                        class="d-flex flex-column justify-content-between"
                      >
                        <div
                          v-for="(shift, sIdx) in jadwal[1]"
                          :key="sIdx"
                          :class="{
                            'bg-soft-primary': shift.libur === 0,
                            'bg-soft-secondary': shift.libur === 1,
                          }"
                        >
                          <div
                            class="pb-1 border-bottom"
                            v-if="shift?.libur === 0"
                          >
                            <div class="d-flex gap-2">
                              <span
                                v-if="shift.locked || !isSuperAdmin"
                                class="fs-12 text-muted my-auto"
                              >
                                {{ shift?.kode_shift }}
                              </span>
                              <v-select
                                v-else-if="!shift.locked && isSuperAdmin"
                                v-model="shift.kode_shift"
                                :options="mShifts"
                                :reduce="(s) => s.kode"
                                label="kode"
                                placeholder="Pilih Shift"
                                @option:selected="
                                  onUpdate({
                                    id: shift.id,
                                    kode_shift: shift.kode_shift,
                                  })
                                "
                              >
                                <template #open-indicator="{ attributes }">
                                  <span v-bind="attributes">🔽</span>
                                </template>
                              </v-select>
                            </div>
                            <div
                              class="badge"
                              :class="{
                                'badge-gradient-success':
                                  shift.status === 'TEPAT',
                                'badge-gradient-warning':
                                  shift.status === 'TELAT',
                                'badge-gradient-danger':
                                  shift.status === 'ALPA',
                                'badge-gradient-info':
                                  shift.status === 'BELUM ABSEN',
                              }"
                            >
                              {{ shift.status }}
                            </div>
                          </div>
                          <div v-else-if="shift.id === null">KOSONG</div>
                          <div
                            v-else-if="
                              shift.libur === 1 && shift.kode_shift === null
                            "
                          >
                            LIBUR
                          </div>
                          <div
                            v-else-if="
                              shift.libur === 1 && shift.kode_shift !== null
                            "
                          >
                            -
                          </div>
                          <i
                            v-if="shift.validate_at !== null"
                            class="ri-lock-2-line fs-14 text-muted"
                            v-b-tooltip="'SUDAH DIAPPROVAL'"
                          ></i>
                          <i
                            v-else
                            class="ri-lock-unlock-line fs-14 text-muted"
                            v-b-tooltip="'BELUM DIAPPROVAL'"
                          ></i>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import {
  SUPER_ADMIN,
  KEPALA,
  KASUB,
  KABID,
  DIREKTUR,
  months,
} from "@/helpers/utils";
import { getYears } from "@/helpers/utils";
import queryString from "query-string";
import { mUnitService } from "@/services/MUnitService";
import { mShiftService } from "@/services/MShiftService";
import { historyJadwalService } from "@/services/HistoryJadwalService";

const initForm = () => {
  return {
    id: "",
    id_unit: "",
    nip: "",
    tanggal: "",
    kode_shift: "",
    type_tanggal: "harian",
  };
};

export default {
  setup() {
    return { v$: useVuelidate() };
  },
  data() {
    return {
      form: initForm(),
      jadwals: [],
      filter: {
        year: "",
        month: "",
        id_unit: "",
      },
      progress: false,
      labels: [],
      rows: [],
      mUnits: [],
      mShifts: [],
      monthList: months,
    };
  },
  validations() {
    return {
      form: {
        tanggal: { required },
      },
    };
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
    isKepala() {
      return this.$store.state.auth.data.role === KEPALA;
    },
    isKaSub() {
      return this.$store.state.auth.data.role === KASUB;
    },
    isKaBid() {
      return this.$store.state.auth.data.role === KABID;
    },
    isDir() {
      return this.$store.state.auth.data.role === DIREKTUR;
    },
  },
  created() {
    this.form.nip = this.$route.params.nip;
    this.getUnitList();
    this.getShift();
    this.years = getYears();
    this.month = new Date().getMonth() + 1;
    this.year = new Date().getFullYear();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async onGetJadwal() {
      this.show();

      let query = queryString.stringify(this.filter, {
        arrayFormat: "index",
      });

      const [err, resp] = await historyJadwalService.getJadwal(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.isLoading = false;
      this.labels = resp.data.label;
      this.rows = resp.data.rows;
      this.hide();
    },

    async getUnitList() {
      this.show();
      const [err, resp] = await mUnitService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.mUnits = resp.data;
      this.hide();
    },

    async getShift() {
      const [err, resp] = await mShiftService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.mShifts = resp.data;
      this.mShifts.unshift({
        id: null,
        kode_shift: "LIBUR",
      });
    },

    async onUpdate(params) {
      const [err] = await historyJadwalService.update(params.id, params);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "OK",
      });
    },
    onReset() {
      this.rows = [];
    },
  },
};
</script>
<style scoped>
td:first-child,
th:first-child {
  position: sticky;
  left: 0;
  background-color: #fff; /* to ensure background color stays solid */
  z-index: 99; /* Ensure the sticky column is above others */
}

th {
  position: sticky;
  top: 0;
  background-color: #f2f2f2;
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
  z-index: 10;
}
</style>
