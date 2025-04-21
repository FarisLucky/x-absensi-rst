<template>
  <div>
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
          @option:selected="fetchData"
        ></v-select>
      </BCol>
      <BCol
        v-if="filter.month > 0 && (isSuperAdmin || isKaBid || isKaSub || isDir)"
        lg="3"
      >
        <v-select
          v-model="filter.unit"
          :options="units"
          :reduce="(l) => l.id"
          label="nama"
          placeholder="Pilih Unit"
          @option:selected="fetchData"
        ></v-select>
      </BCol>
      <BCol>
        <BButton
          type="button"
          variant="success"
          class="me-1"
          @click="exportExcel"
        >
          <i class="ri-file-excel-2-line me-1 align-bottom"></i>
          Export
        </BButton>
        <BButton type="button" variant="outline-secondary" @click="onRefresh">
          <i class="ri-refresh-fill me-1 align-bottom"></i>
          Reset
        </BButton>
      </BCol>
      <div class="sticky-cover">
        <vue-good-table
          mode="local"
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
          styleClass="vgt-table bordered striped sticky"
        >
          <template #table-row="props">
            <!-- <span>{{ props.formattedRow[props.column.field] }}</span>
            <span>{{ props.formattedRow[props.column.field].index }}</span> -->
            <span
              v-for="field in props.formattedRow[props.column.field]"
              :key="field"
            >
              <div
                class="p-1 rounded text-center"
                :class="{
                  'bg-secondary text-white': field == 'L',
                  'bg-success text-light':
                    field.toString().includes(':') &&
                    field.split(':')[1].trim() == 'TEPAT',
                  'bg-warning text-light':
                    field.toString().includes(':') &&
                    field.split(':')[1].trim() == 'TELAT',
                  'bg-danger text-light':
                    field.toString().includes(':') &&
                    field.split(':')[1].trim() == 'ALPA',
                  'bg-info text-white':
                    field.toString().includes(':') &&
                    field.split(':')[1].trim() == 'IZIN',
                }"
              >
                <span>{{ field }}</span>
              </div>
            </span>
          </template>
        </vue-good-table>
      </div>
    </BRow>
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { historyPresensiService } from "@/services/HistoryPresensiService";
import {
  DIREKTUR,
  getYears,
  KABID,
  KASUB,
  months,
  SUPER_ADMIN,
} from "@/helpers/utils";
import queryString from "query-string";
import { mUnitService } from "@/services/MUnitService";
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
      columns: [],
      rows: [],
      isLoading: false,
      jadwalku: null,
      listShift: [],
      pengembangan: false,
      tanggal: null,
      months,
      years: [],
      units: [],
    };
  },
  created() {
    this.years = getYears();
    this.onRefresh();
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
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await historyPresensiService.progress(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.columns = resp.data.columns.map((column, idx) => {
        let labelTxt = "";
        if (idx > 0) {
          labelTxt = "";
        }
        return {
          label: labelTxt + column.toString().toUpperCase(),
          field: column.toString(),
        };
      });
      this.rows = resp.data.rows;
      this.isLoading = false;
    },
    onRefresh() {
      this.fetchData();
      this.getUnit();
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
    onShowGrafik() {},
    onShowTable() {},
    exportExcel() {
      let query = queryString.stringify(
        Object.assign(this.filter, { nip: this.$store.state.auth.data.nip }),
        {
          arrayFormat: "index",
        }
      );
      let url = `${webUrl}/rekap/presensi-bulanan/export?${query}`;
      const a = document.createElement("a");
      a.href = url;
      a.setAttribute("target", "_blank");
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    },
  },
};
</script>
