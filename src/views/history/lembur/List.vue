<template>
  <div>
    <div class="mb-1">
      <h5 class="pb-1 d-inline-block border-bottom">Lembur Selesai</h5>
      <BForm autocomplete="off">
        <BRow class="g-2">
          <BCol lg="2">
            <div class="search-box">
              <input
                :value="filter.search"
                type="text"
                class="form-control search bg-light border-light"
                placeholder="Cari Karyawan disini..."
                @input="onFilterSearchFn"
                @keyup.enter="fetchData"
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
          <BCol v-if="isSuperAdmin || isKaSub || isKaBid || isDir" lg="3">
            <v-select
              :modelValue="filter.unit"
              :options="unitList"
              :reduce="(unit) => unit.id"
              label="nama"
              placeholder="Pilih Unit"
              @update:modelValue="onFilterUnitFn"
            ></v-select>
          </BCol>
          <BCol>
            <button
              type="button"
              class="btn btn-info btn-label waves-effect waves-light me-1"
              @click="fetchData"
            >
              <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
              Cari
            </button>
            <BButton
              type="button"
              variant="success"
              class="me-1"
              @click="onExport"
            >
              <i class="ri-file-excel-2-line me-1 align-bottom"></i>
            </BButton>
            <BButton
              type="button"
              variant="outline-secondary"
              @click="onRefresh"
              class="me-1"
            >
              <i class="ri-refresh-fill me-1 align-bottom"></i>
            </BButton>
          </BCol>
        </BRow>
      </BForm>
    </div>
    <div class="mb-1 table-responsive">
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
        :isLoading="isLoading"
        theme="polar-bear"
        :line-numbers="true"
        styleClass="vgt-table striped sticky"
      >
        <template #table-row="props">
          <span v-if="props.column.field === 'nama'">
            <div class="d-flex">
              <img
                v-if="props.row.pemohon?.photo !== null"
                :src="props.row.pemohon?.photo_url_cast"
                class="avatar-sm me-1 rounded material-shadow"
              />
              <img
                v-else
                src="@/assets/images/profil.jpg"
                class="avatar-sm me-1 rounded material-shadow"
                width="30px"
              />
              <div class="d-flex flex-column gap-1 align-items-center">
                <strong class="mb-1">{{ props.row?.nama }}</strong>
                <div class="badge badge-gradient-info">
                  {{ props.row.m_unit?.nama }}
                </div>
              </div>
            </div>
          </span>
          <span v-if="props.column.field === 'tanggal_cast'">
            <h5 class="mb-1">{{ props.row.tgl_day_cast }}</h5>
            <span>{{ props.row.tanggal_cast }}</span>
          </span>
          <span v-if="props.column.field === 'akhir_cast'">
            <span>
              {{ props.row.akhir_cast }}
              <i
                class="ri-information-line fs-14 text-primary"
                v-b-tooltip="`Tanggal: ${props.row.akhir}`"
              ></i>
            </span>
          </span>
          <span v-if="props.column.field === 'ttl_jam'">
            <h5 class="mb-1">{{ props.row.ttl_jam }} Jam</h5>
          </span>
          <span v-if="props.column.field === 'aksi'">
            <BButton
              variant="soft-primary"
              size="sm"
              @click.prevent="
                onShow({
                  id: props.row.id,
                })
              "
            >
              <i class="ri-play-line"></i>
            </BButton>
          </span>
        </template>
      </vue-good-table>
    </div>
    <keterangan ref="keteranganRef" />
    <LemburDetail ref="lemburRef" />
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  historyLemburMethods,
  historyLemburState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import queryString from "query-string";
import { SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { webUrl } from "@/config/http";
import Keterangan from "@/views/izinku/Keterangan";
import LemburDetail from "@/views/history/lembur/LemburDetail";
import { historyLemburService } from "@/services/HistoryLemburService";
import flatPickr from "vue-flatpickr-component";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

export default {
  components: {
    VueGoodTable,
    Keterangan,
    LemburDetail,
    flatPickr,
  },
  data() {
    const user = this.$store.state?.auth?.data;

    return {
      columns: [
        {
          label: "Pengajuan",
          field: "created_at_cast",
        },
        {
          label: "Karyawan",
          field: "nama",
        },
        {
          label: "Catatan",
          field: "catatan",
        },
        {
          label: "Tanggal",
          field: "tanggal_cast",
        },
        {
          label: "Mulai",
          field: "mulai_cast",
        },
        {
          label: "Akhir",
          field: "akhir_cast",
        },
        {
          label: "Total Jam",
          field: "ttl_jam",
        },
        {
          label: "Status",
          field: "status_desc",
        },
        {
          label: "Aksi",
          field: "aksi",
        },
      ],
      rows: [],
      totalRecords: 0,
      isLoading: false,
      user,
      unitList: [],
    };
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
  computed: {
    ...historyLemburState,
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  created() {
    this.onRefresh();
    this.getUnitList();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...historyLemburMethods,
    async fetchData() {
      this.isLoading = true;
      const query = queryString.stringify(
        Object.assign({}, this.server, this.filter),
        {
          arrayFormat: "index",
        }
      );

      const [err, resp] = await historyLemburService.all(query);
      if (err) {
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
      this.fetchData();
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
        this.hide();
      }
    },
    async onShow(params) {
      this.show();
      const [err, resp] = await historyLemburService.show(params.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.$refs.lemburRef.lembur = resp.data;
      this.hide();
      this.$refs.lemburRef.showModal();
    },
    showKeterangan(params) {
      this.$refs.keteranganRef.title = params.title;
      this.$refs.keteranganRef.ket = params.ket;
      this.$refs.keteranganRef.showModal();
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
      this.onFilterRangeTanggal(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    showFoto(params) {
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    onExport() {
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      let a = document.createElement("a");
      a.href = `${webUrl}/rekap/lembur/excel?${query}`;
      a.setAttribute("target", "_blank");
      a.click();
    },
  },
};
</script>
