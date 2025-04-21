<template>
  <BCard no-body class="mb-1">
    <BCardHeader class="pb-1">
      <BCardTitle>
        <h5>Harian Kerja Karyawan PT Catur Karsa Inkrisuba</h5>
      </BCardTitle>
    </BCardHeader>
    <BCardBody>
      <form @submit.prevent="fetchData" autocomplete="off">
        <BRow class="g-2">
          <BCol cols="6" md="3">
            <div class="search-box">
              <input
                type="text"
                class="form-control search bg-light border-light"
                placeholder="Cari Karyawan disini..."
                @input="onFilterSearchFn"
                :value="filter.search"
              />
              <i class="ri-search-line search-icon"></i>
            </div>
          </BCol>
          <BCol cols="6" md="2">
            <flat-pickr
              :modelValue="filter.tanggal"
              @update:modelValue="onFilterTanggalFn"
              placeholder="Pilih Tanggal"
              :config="{
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
                wrap: true, // set wrap to true only when using 'input-group'
              }"
              class="form-control bg-light border-light"
              required
            ></flat-pickr>
          </BCol>
          <BCol cols="6" md="2">
            <div class="input-group w-100">
              <v-select
                :modelValue="filter.shift"
                @update:modelValue="onFilterShiftFn"
                :options="['PAGI', 'SIANG', 'MALAM']"
                placeholder="Pilih Shift"
              ></v-select>
              <BButton
                variant="secondary"
                size="sm"
                @click.prevent="onFilterShiftFn(null)"
              >
                <i class="ri-close-fill"></i>
              </BButton>
            </div>
          </BCol>
          <BCol cols="6" md="3">
            <div class="input-group w-100">
              <v-select
                :modelValue="filter.unit"
                @update:modelValue="onFilterUnitFn"
                :options="unitOpt"
                :reduce="(l) => l.id"
                label="nama"
                placeholder="Pilih Unit"
                class="w-75"
              ></v-select>
              <BButton
                variant="secondary"
                size="sm"
                @click.prevent="onFilterShiftFn(null)"
              >
                <i class="ri-close-fill"></i>
              </BButton>
            </div>
          </BCol>
          <BCol>
            <div class="text-end">
              <BButton type="submit" variant="info" class="me-1">
                <i class="ri-file-search-line"></i>
                Cari
              </BButton>
              <BButton
                type="button"
                variant="soft-secondary"
                @click="onRefresh"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
              </BButton>
            </div>
          </BCol>
        </BRow>
      </form>
    </BCardBody>
    <BCardBody class="pt-0">
      <div class="mb-1">
        <vue-good-table
          mode="remote"
          v-on:page-change="onPageChange"
          v-on:sort-change="onSortChange"
          v-on:per-page-change="onPerPageChange"
          :totalRows="totalRecords"
          :columns="columns"
          :rows="rows"
          :search-options="{
            enabled: true,
            externalQuery: filter.search,
          }"
          :sort-options="{
            enabled: false,
          }"
          :pagination-options="{
            enabled: true,
            perPage: 10,
            perPageDropdown: [10, 20],
          }"
          :line-numbers="true"
          :isLoading="isLoading"
          theme="polar-bear"
          styleClass="vgt-table striped sticky"
        >
          <template #table-row="props">
            <div v-if="props.column.field == 'nama'" class="d-flex">
              <img
                v-if="props.row.m_karyawan?.photo !== null"
                :src="props.row.m_karyawan?.photo_url_cast"
                class="avatar-sm me-1 rounded material-shadow"
              />
              <img
                v-else
                src="@/assets/images/profil.jpg"
                class="avatar-sm me-1 rounded material-shadow"
                width="30px"
              />
              <div class="p-2">
                <strong class="mb-1 d-block">{{ props.row?.nama }}</strong>
                <div class="badge badge-gradient-info">
                  {{ props.row.unit ?? "belum ada unit" }}
                </div>
              </div>
            </div>
            <span v-if="props.column.field == 'tanggal'">
              <h5 class="mb-1">{{ props.row.day_cast }}</h5>
              {{ `${props.row.tanggal_cast}` }}
            </span>
            <span v-if="props.column.field == 'kode_shift'">
              <div v-if="props.row.libur < 1">
                <h5 class="mb-1">{{ props.row.shift }}</h5>
                <span class="text-muted">
                  {{ `${props.row.jam_masuk} - ${props.row.jam_pulang}` }}
                </span>
              </div>
              <h5 v-else class="text-muted">Libur</h5>
            </span>
            <span v-if="props.column.field == 'ttlkerja'">
              <h6 v-if="props.row.ttlkerja !== null" class="mb-1">
                {{ convertMinute(props.row.ttlkerja) }}
              </h6>
            </span>
            <span v-if="props.column.field == 'masuk'">
              <h5 class="mb-1">{{ props.row.masuk }}</h5>
              <BButton
                v-if="
                  props.row.masuk !== null && props.row.latlng_masuk !== null
                "
                variant="outline-primary"
                size="sm"
                @click.prevent="
                  onShowLokasi({
                    id: props.row.id,
                    jenis: 'MASUK',
                    lokasi: props.row?.lok_masuk,
                    latlng: props.row?.latlng_masuk,
                  })
                "
              >
                <i class="ri-user-location-line"></i>
                Lokasi {{ props.row?.lok_masuk }}
              </BButton>
            </span>
            <span v-if="props.column.field == 'pulang'">
              <h5 class="mb-1">{{ props.row.pulang }}</h5>
              <BButton
                v-if="
                  props.row.pulang !== null && props.row.latlng_pulang !== null
                "
                variant="outline-primary"
                size="sm"
                @click.prevent="
                  onShowLokasi({
                    id: props.row.id,
                    jenis: 'PULANG',
                    lokasi: props.row?.lok_pulang,
                    latlng: props.row?.latlng_pulang,
                  })
                "
              >
                <i class="ri-user-location-line"></i>
                Lokasi {{ props.row.lok_pulang }}
              </BButton>
            </span>
            <span v-if="props.column.field == 'status'">
              <span
                class="badge border fs-11"
                :class="{
                  'border-secondary text-secondary': props.row.status === null,
                  'border-info text-info':
                    props.row.status === JADWAL_STATUS.PROGRESS,
                  'border-primary text-primary':
                    props.row.status === JADWAL_STATUS.SELESAI,
                  'border-warning text-warning':
                    props.row.status === JADWAL_STATUS.IZIN,
                  'border-danger text-danger':
                    props.row.status === JADWAL_STATUS.TIDAK_HADIR,
                }"
              >
                {{ props.row.status_cast }}
              </span>
              <br v-if="props.row.status_absen !== null" />
              <span
                v-if="props.row.status_absen !== null"
                class="badge bg-info-subtle text-info fs-11 mt-1"
              >
                {{ props.row.status_absen }}
                <i
                  class="ri-information-line fs-12"
                  v-b-tooltip="
                    `Alasan Telat: ${props.row.presensi_terlambat?.ket ?? '-'}`
                  "
                ></i>
              </span>
            </span>
            <span v-if="props.column.field == 'aksi'">
              <router-link
                v-if="props.row.izin_detail != null"
                class="btn btn-sm btn-primary"
                :to="{
                  name: 'HistoryIzinDetail',
                  params: { id: props.row.izin_detail?.id_izin },
                }"
                v-b-tooltip="'Detail Izin'"
              >
                <i class="ri-play-line"></i>
              </router-link>
              <router-link
                class="btn btn-sm btn-soft-info"
                :to="{
                  name: 'PresensiDetail',
                  params: { id: props.row.id },
                }"
                v-b-tooltip="'Detail Presensi'"
              >
                <i class="ri-play-line"></i>
              </router-link>
            </span>
          </template>
        </vue-good-table>
      </div>
    </BCardBody>
    <ShowLokasi ref="showLokasiRef" />
  </BCard>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  harianMethods,
  harianState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";
import { harianService } from "@/services/HarianService";
import queryString from "query-string";
import { mUnitService } from "@/services/MUnitService";
import { webUrl } from "@/config/http";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { JADWAL_STATUS } from "@/helpers/utils";
import { convertMinutesToHours } from "@/helpers/format";
import ShowLokasi from "@/views/history/presensi/ShowLokasi.vue";

export default {
  components: {
    flatPickr,
    VueGoodTable,
    ShowLokasi,
  },
  data() {
    return {
      columns: [
        {
          label: "Hari",
          field: "day_cast",
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Shift",
          field: "kode_shift",
        },
        {
          label: "Masuk",
          field: "masuk",
        },
        {
          label: "Pulang",
          field: "pulang",
        },
        {
          label: "Waktu",
          field: "waktu",
        },
        {
          label: "Status",
          field: "status",
        },
        {
          label: "Aksi",
          field: "aksi",
        },
      ],
      rows: [],
      totalRecords: 0,
      isLoading: false,
      listShift: [],
      unitOpt: [],
      izinOpt: [],
      JADWAL_STATUS,
    };
  },
  computed: {
    ...harianState,
  },
  created() {
    this.fetchData();
    this.getUnit();
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  watch: {
    reload() {
      this.fetchData();
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...harianMethods,
    async fetchData() {
      this.isLoading = true;

      let query = queryString.stringify(
        Object.assign({}, this.filter, this.server),
        {
          arrayFormat: "index",
        }
      );

      const [err, resp] = await harianService.kerja(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.rows = resp.data.data;
      this.totalRecords = resp.data.total;
      this.isLoading = false;
    },
    async getUnit() {
      this.show();
      const [err, resp] = await mUnitService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.unitOpt = resp.data;
      this.hide();
    },
    onRefresh() {
      this.resetFilter();
      this.fetchData();
    },
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
    showBukti(params) {
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    onFilterSearchFn(event) {
      let val = event.target.value;
      if (val.length > 2) {
        this.onFilterSearch(val);
      }
      if (val === "") {
        this.onFilterSearch("");
      }
    },
    onFilterTanggalFn(val) {
      this.onFilterTanggal(val);
    },
    onFilterShiftFn(val) {
      this.onFilterShift(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    convertMinute(minutes) {
      const { hours, remainingMinutes } = convertMinutesToHours(minutes);
      return `${hours} J : ${remainingMinutes} M`;
    },
    onShowLokasi(params) {
      let lokasi = this.$refs.showLokasiRef;
      let latitude = params.latlng.split("/")[0];
      let longitude = params.latlng.split("/")[1];
      lokasi.form.id = params.id;
      lokasi.form.jenis = params.jenis;
      lokasi.form.lokasi = params.lokasi;
      lokasi.form.latitude = latitude;
      lokasi.form.longitude = longitude;
      lokasi.showModal();
    },
  },
};
</script>
