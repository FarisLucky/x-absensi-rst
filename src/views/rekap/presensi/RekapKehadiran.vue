<template>
  <div class="p-2">
    <BRow class="g-2 align-items-end mb-1">
      <BCol :lg="3">
        <label for="tanggal" class="form-label">
          Range
          <span class="text-danger">*</span>
        </label>
        <flat-pickr
          :modelValue="filter.range_tanggal"
          @update:modelValue="onFilterRangeFn"
          placeholder="Pilih Range Tanggal"
          :config="{
            mode: 'range',
            dateFormat: 'Y-m-d',
          }"
          class="form-control bg-light border-light"
          required
        ></flat-pickr>
      </BCol>
      <BCol lg="3">
        <label for="unit" class="form-label">
          Unit
          <span class="text-danger">*</span>
        </label>
        <v-select
          multiple
          :modelValue="filter.unit"
          :options="unitList"
          :reduce="(l) => l.id"
          label="nama"
          placeholder="Pilih Unit"
          @update:modelValue="onFilterUnitFn"
        ></v-select>
      </BCol>
      <BCol :lg="3">
        <label for="status" class="form-label"> Status </label>
        <v-select
          :modelValue="filter.status"
          :options="statusOpt"
          placeholder="Pilih Status"
          :reduce="(s) => s.kode"
          @update:modelValue="onFilterStatusFn"
        ></v-select>
      </BCol>
      <div class="col-lg">
        <button
          type="submit"
          class="btn btn-info btn-label waves-effect waves-light me-1"
          @click="fetchData"
        >
          <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
          Cari
        </button>
        <BButton
          type="button"
          variant="outline-secondary"
          @click="onRefresh"
          class="me-1"
        >
          <i class="ri-refresh-fill me-1 align-bottom"></i>
        </BButton>
      </div>
    </BRow>
    <BRow v-if="rows?.length > 0" class="g-2">
      <BCol :md="12">
        <vue-good-table
          mode="local"
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: true,
          }"
          :line-numbers="true"
          :isLoading="isLoading"
          theme="polar-bear"
          styleClass="vgt-table striped sticky"
        >
          <template #table-row="props">
            <span v-if="props.column.field == 'desc'">
              <span v-if="status == 'TL'">
                Absen Terlambat Sebanyak
                <strong class="p-1 text-warning">{{
                  props.row.ttl ?? "-"
                }}</strong>
                x
              </span>
              <span v-if="status == 'TF'">
                Tukar Off Sebanyak
                <strong class="p-1 text-success">{{
                  props.row.ttl ?? "-"
                }}</strong>
                x
              </span>
              <span v-if="status == 'A'">
                Absen Alpa Sebanyak
                <strong class="p-1 text-danger">{{
                  props.row.ttl ?? "-"
                }}</strong>
                x
              </span>
              <span v-if="status == 'SS'">
                Izin Sakit Sebanyak
                <strong class="p-1 text-success">{{
                  props.row.ttl ?? "-"
                }}</strong>
                x
              </span>
            </span>
          </template>
        </vue-good-table>
      </BCol>
    </BRow>
    <div v-else>
      <h4 class="text-center fs-14 rounded px-1 py-3 border">
        <Lottie
          colors="primary:#121331,secondary:#08a88a"
          trigger="loop"
          :options="{
            animationData: animationData4,
          }"
          :height="70"
          :width="70"
        />
        Belum ada Data
      </h4>
    </div>
  </div>
</template>
<script>
import { webUrl } from "@/config/http";
import { DIREKTUR, KABID, KASUB, SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import {
  rekapKehadiranMethods,
  rekapKehadiranState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import queryString from "query-string";
import flatPickr from "vue-flatpickr-component";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import Lottie from "@/components/widgets/lottie.vue";
import animationData4 from "@/components/widgets/pithnlch.json";
import { rekapService } from "@/services/RekapService";

export default {
  components: {
    flatPickr,
    VueGoodTable,
    Lottie,
  },
  data() {
    return {
      webUrl,
      columns: [
        {
          label: "Unit",
          field: "unit",
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Deskripsi",
          field: "desc",
        },
      ],
      rows: [],
      isLoading: false,
      unitList: [],
      statusOpt: [
        {
          kode: "TL",
          label: "Sering Telat",
        },
        {
          kode: "A",
          label: "Sering Alpa",
        },
      ],
      animationData4,
      status: null,
    };
  },
  computed: {
    ...rekapKehadiranState,
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
    this.getUnitList();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...rekapKehadiranMethods,
    async fetchData() {
      this.rows = [];
      if (this.filter.range_tanggal === "" || this.filter.unit === "") {
        this.toastError({
          title: "Gagal",
          msg: "Form Wajib tidak boleh kosong",
        });
        return;
      }
      this.isLoading = true;
      this.show();
      const query = queryString.stringify(Object.assign({}, this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await rekapService.kehadiran(query);
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
      (this.status = this.filter.status), (this.isLoading = false);
      this.hide();
    },
    async getUnitList() {
      if (this.isSuperAdmin || this.isKaSub || this.isKaBid || this.isDir) {
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
        this.unitList.unshift({
          id: -1,
          nama: "SEMUA",
        });
        this.hide();
      }
    },
    onRefresh() {
      this.resetFilter();
      this.fetchData();
    },
    onExport() {
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      let a = document.createElement("a");
      a.href = `${webUrl}/rekap/izin/export?${query}`;
      a.setAttribute("target", "_blank");
      a.click();
    },
    onFilterRangeFn(val) {
      this.onFilterRange(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    onFilterStatusFn(val) {
      this.onFilterStatus(val);
    },
  },
};
</script>
