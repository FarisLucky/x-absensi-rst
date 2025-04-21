<template>
  <div>
    <div class="mb-1">
      <h5 class="pb-1 d-inline-block border-bottom">Presensi Selesai</h5>
      <form class="mb-3">
        <BRow class="g-2">
          <BCol lg="2">
            <div class="search-box">
              <input
                type="text"
                class="form-control search bg-light border-light"
                placeholder="Cari Karyawan..."
                @input="onFilterSearchFn"
                :value="filter.search"
                @keyup.enter="onSearch"
              />
              <i class="ri-search-line search-icon"></i>
            </div>
            <small class="mb-0 text-danger">
              <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
              Enter untuk mencari
            </small>
          </BCol>
          <BCol lg="2">
            <flat-pickr
              :modelValue="filter.range_tanggal"
              @update:modelValue="onFilterRangeFn"
              placeholder="Pilih Range Tanggal"
              :config="{
                mode: 'range',
                dateFormat: 'd-m-Y',
              }"
              class="form-control bg-light border-light"
              required
            ></flat-pickr>
          </BCol>
          <BCol v-if="isSuperAdmin" lg="3">
            <v-select
              :modelValue="filter.unit"
              :options="unitList"
              :reduce="(unit) => unit.id"
              label="nama"
              placeholder="Pilih Unit"
              @update:modelValue="onFilterUnitFn"
            ></v-select>
          </BCol>
          <BCol lg="2">
            <v-select
              :modelValue="filter.status"
              @update:modelValue="onFilterStatusFn"
              :options="['HADIR', 'TEPAT', 'TELAT', 'TIDAK HADIR']"
              placeholder="Pilih Status"
            ></v-select>
          </BCol>
          <BCol>
            <button
              type="button"
              class="btn btn-info btn-label waves-effect waves-light me-1"
              @click="onSearch"
            >
              <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
              Cari
            </button>
            <BButton
              type="button"
              variant="success"
              class="me-1"
              @click="exportExcel"
            >
              <i class="ri-file-excel-2-line me-1 align-bottom"></i>
            </BButton>
            <BButton
              type="button"
              variant="outline-secondary"
              @click="onRefresh"
            >
              <i class="ri-refresh-fill me-1 align-bottom"></i>
            </BButton>
          </BCol>
        </BRow>
      </form>
    </div>
    <div class="mb-1">
      <vue-good-table
        mode="remote"
        v-on:page-change="onPageChange"
        v-on:sort-change="onSortChange"
        v-on:per-page-change="onPerPageChange"
        :totalRows="totalRecords"
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: true,
        }"
        :sort-options="{
          enabled: false,
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
          <span v-if="props.column.field == 'masuk'">
            <h5 class="mb-1">{{ props.row.masuk }}</h5>
            <BButton
              v-if="props.row.masuk !== null && props.row.latlng_masuk !== null"
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
          <span v-if="props.column.field == 'ttlkerja'">
            <h6 v-if="props.row.ttlkerja !== null" class="mb-1">
              {{ convertMinute(props.row.ttlkerja) }}
            </h6>
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
    <ShowLokasi ref="showLokasiRef" />
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  historyPresensiMethods,
  historyPresensiState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import { historyPresensiService } from "@/services/HistoryPresensiService";
import queryString from "query-string";
import { SUPER_ADMIN, JADWAL_STATUS } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import flatPickr from "vue-flatpickr-component";
import { presensiService } from "@/services/PresensiService";
import { webUrl } from "@/config/http";
import { convertMinutesToHours } from "@/helpers/format";
import ShowLokasi from "./ShowLokasi.vue";

export default {
  components: {
    VueGoodTable,
    flatPickr,
    ShowLokasi,
  },
  data() {
    return {
      columns: [
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Tanggal",
          field: "tanggal",
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
          label: "Total",
          field: "ttlkerja",
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
      jadwalku: null,
      listShift: [],
      karyawanList: [],
      unitList: [],
      JADWAL_STATUS,
    };
  },
  computed: {
    ...historyPresensiState,
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  created() {
    this.getUnitList();
    this.onSearch();
  },
  watch: {
    reload() {
      this.onSearch();
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...historyPresensiMethods,
    async getUnitList() {
      if (this.isSuperAdmin) {
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
        this.unitList = resp.data;
        this.hide();
      }
    },
    async onUpdateStatus(params) {
      this.show();
      const [err] = await presensiService.updateStatus(
        params.status,
        params.id
      );
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.toastSuccess({
        title: "Gagal",
        msg: "OK",
      });
      this.hide();
    },
    async onSearch() {
      this.isLoading = true;
      const query = queryString.stringify(
        Object.assign({}, this.server, this.filter),
        {
          arrayFormat: "index",
        }
      );

      const [err, resp] = await historyPresensiService.searchByKaryawan(query);
      if (err) {
        console.log(err);
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      let pagination = resp.data;
      this.rows = pagination.data;
      this.totalRecords = pagination.total;
      this.isLoading = false;
    },
    onRefresh() {
      this.resetFilter();
      this.onSearch();
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
    onFilterRangeFn(val) {
      this.onFilterRange(val);
    },
    onFilterStatusFn(val) {
      this.onFilterStatus(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    exportExcel() {
      let query = queryString.stringify(
        Object.assign({}, this.filter, {
          nip: this.$store.state.auth.data.nip,
        }),
        {
          arrayFormat: "index",
        }
      );
      let url = `${webUrl}/history/presensi-harian/export?${query}`;
      const a = document.createElement("a");
      a.href = url;
      a.setAttribute("target", "_blank");
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
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
