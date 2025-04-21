<template>
  <div>
    <div class="mb-1">
      <form @submit.prevent="fetchData">
        <BRow class="g-2">
          <BCol lg="2">
            <div class="search-box">
              <input
                type="text"
                class="form-control search bg-light border-light"
                placeholder="Cari Karyawan disini..."
                v-model="filter.search"
              />
              <i class="ri-search-line search-icon"></i>
            </div>
          </BCol>
          <BCol lg="2">
            <v-select
              v-model="filter.year"
              :options="years"
              placeholder="Pilih Tahun"
            ></v-select>
          </BCol>
          <BCol v-if="filter.year > 0" lg="2">
            <v-select
              v-model="filter.month"
              :options="months"
              :reduce="(l) => l.id"
              label="name"
              placeholder="Pilih Bulan"
            ></v-select>
          </BCol>
          <BCol lg="3">
            <v-select
              v-model="filter.unit"
              :options="units"
              :reduce="(l) => l.id"
              label="nama"
              placeholder="Pilih Unit"
            ></v-select>
          </BCol>
          <BCol>
            <BButton
              type="submit"
              variant="primary"
              class="me-1"
              @click="getData"
            >
              <i class="ri-search-2-line me-1 align-bottom"></i>
              Cari
            </BButton>
            <a
              :href="setExportUrl()"
              target="_blank"
              class="btn btn-success me-1"
            >
              <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
            </a>
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
        mode="local"
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: true,
        }"
        :total-rows="totalRecords"
        :isLoading="isLoading"
        :line-numbers="true"
        :sort-options="{
          enabled: false,
        }"
        theme="polar-bear"
        styleClass="vgt-table striped sticky"
        @on-page-change="onPageChange"
        @on-per-page-change="onPerPageChange"
      >
        <template #table-row="props">
          <span v-if="props.column.field === 'persentase'">
            {{
              Math.round(
                (parseInt(props.row.hadir) / parseInt(props.row.hari_kerja)) *
                  100
              )
            }}
            %
          </span>
          <span v-if="props.column.field === 'catatan'"> lorem ipsum </span>
          <span v-if="props.column.field === 'nama'">
            <div class="d-flex">
              <img
                v-if="props.row?.photo !== null"
                :src="props.row?.photo_url_cast"
                class="avatar-sm me-1 rounded material-shadow"
              />
              <img
                v-else
                src="@/assets/images/profil.jpg"
                class="avatar-sm me-1 rounded material-shadow"
                width="30px"
              />
              <div class="p-2">
                <strong class="mb-1 d-block">
                  {{ props.row?.nama }}
                </strong>
                <div class="badge badge-gradient-info">
                  {{ props.row.m_unit?.nama }}
                </div>
              </div>
            </div>
          </span>
        </template>
      </vue-good-table>
    </div>
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import {
  months,
  getYears,
  SUPER_ADMIN,
  KASUB,
  KABID,
  DIREKTUR,
} from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { historyKaryawanService } from "@/services/HistoryKaryawanService";
import { webUrl } from "@/config/http";

const initFilter = () => {
  const currentTime = new Date();

  return {
    search: "",
    month: currentTime.getMonth() + 1,
    year: currentTime.getFullYear(),
    unit: "",
  };
};

export default {
  components: {
    VueGoodTable,
  },
  data() {
    return {
      filter: initFilter(),
      columns: [
        {
          field: "nip",
          hidden: true,
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Total Hari Kerja",
          field: "hari_kerja",
        },
        {
          label: "Hadir (kali)",
          field: "hadir",
        },
        {
          label: "Terlambat (kali)",
          field: "telat",
        },
        {
          label: "Alpa (kali)",
          field: "alpa",
        },
        {
          label: "Kehadiran (%)",
          field: "persentase",
        },
        {
          label: "Catatan",
          field: "catatan",
        },
      ],
      rows: [],
      isLoading: false,
      totalRecords: 0,
      months,
      years: [],
      units: [],
      currentPage: 1,
      perPage: 10,
      sortField: "id",
      sortType: "asc",
    };
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
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
    this.onRefresh();
    this.years = getYears();
    this.getUnit();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    getData() {
      this.fetchData();
    },
    async fetchData() {
      this.isLoading = true;
      this.show();
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await historyKaryawanService.kinerjaStaf(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;
        this.hide();

        return;
      }
      let result = resp.data;
      this.rows = result.data;
      this.isLoading = false;
      this.hide();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
      // this.rows = [];
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
      this.units = resp.data;
      this.hide();
    },
    setExportUrl() {
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      return `${webUrl}/api/rekap/karyawan/export?${query}`;
    },
    onPageChange(params) {
      this.currentPage = params.currentPage;
      this.fetchData();
    },
    onSortChange(params) {
      this.sortField = params[0].field;
      this.sortType = params[0].type;
      this.fetchData();
    },

    onPerPageChange(params) {
      this.perPage = params.currentPerPage;
      this.currentPage = 1; // Reset to the first page
      this.fetchData();
    },

    refreshData() {
      this.currentPage = 1;
      this.fetchData();
    },
  },
};
</script>
