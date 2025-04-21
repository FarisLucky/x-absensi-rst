<template>
  <div>
    <div class="mb-1">
      <BForm>
        <BRow class="g-2">
          <BCol lg="2">
            <div class="search-box">
              <input
                type="text"
                class="form-control search bg-light border-light"
                placeholder="Cari Karyawan disini..."
                @input="searchFn"
                :value="filter.search"
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
          <BCol
            v-if="
              filter.month > 0 && (isKaBid || isKaSub || isDir || isSuperAdmin)
            "
            lg="3"
          >
            <v-select
              v-model="filter.unit"
              :options="units"
              :reduce="(l) => l.id"
              label="nama"
              placeholder="Pilih Unit"
              @option:selected="getData"
            ></v-select>
          </BCol>
          <BCol>
            <a
              :href="setExportUrl()"
              target="_blank"
              class="btn btn-success me-1"
            >
              <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
              Export
            </a>
            <BButton
              type="button"
              variant="outline-secondary"
              @click="onRefresh"
            >
              <i class="ri-refresh-fill me-1 align-bottom"></i>
              Reset
            </BButton>
          </BCol>
        </BRow>
      </BForm>
    </div>
    <div v-if="rows.length > 0" class="mb-1">
      <vue-good-table
        mode="local"
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: true,
          perPage: 100,
          perPageDropdown: [100, 200],
        }"
        :sort-options="{
          enabled: true,
          multipleColumns: true,
          initialSortBy: { field: 'tepat', type: 'desc' },
        }"
        :isLoading="isLoading"
        :line-numbers="true"
        theme="polar-bear"
        styleClass="vgt-table striped sticky"
      >
        <!-- <template #table-row="props"> </template> -->
      </vue-good-table>
    </div>
    <div v-else>
      <h4 class="p-3 border rounded text-center">Data belum dipilih</h4>
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
import { IZIN_CUTI, IZIN_KRS } from "@/helpers/utils.js";
import { historyKaryawanService } from "@/services/HistoryKaryawanService";
import { webUrl } from "@/config/http";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

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
    const user = this.$store.state?.auth?.data;

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
          label: "Unit",
          field: "m_unit.nama",
        },
        {
          label: "Jadwal",
          field: "jadwal",
          tdClass: "bg-info text-white",
        },
        {
          label: "Tepat",
          field: "tepat",
          tdClass: "bg-success text-light",
        },
        {
          label: "Telat",
          field: "telat",
          tdClass: "bg-warning text-light",
        },
        {
          label: "Alpa",
          field: "alpa",
          tdClass: "bg-danger text-light",
        },
      ],
      rows: [],
      isLoading: false,
      jadwalku: null,
      listShift: [],
      months,
      years: [],
      user,
      units: [],
      IZIN_CUTI,
      IZIN_KRS,
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
  directives: {
    viewer: viewer({
      debug: false,
    }),
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

      const [err, resp] = await historyKaryawanService.all(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;
        this.hide();

        return;
      }
      this.rows = resp.data;
      this.isLoading = false;
      this.hide();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
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
    getBukti(id) {
      let bukti = [];
      let file = `${webUrl}/izin/bukti/${id}`;

      bukti.push(file);

      return bukti;
    },
    showBukti(params) {
      // const viewer = params.$viewer;
      console.log(params);
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    searchFn(event) {
      let search = event.target.value;
      if (search.length > 2) {
        this.filter.search = search;
        this.fetchData();
      }
      if (search === "") {
        this.filter.search = "";
        this.rows = [];
      }
    },
    setExportUrl() {
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      return `${webUrl}/api/rekap/karyawan/export?${query}`;
    },
  },
};
</script>
