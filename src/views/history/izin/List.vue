<template>
  <BCard no-body>
    <BCardBody>
      <h5 class="pb-1 d-inline-block border-bottom">Izin Selesai</h5>
      <form>
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
          <BCol :lg="2">
            <v-select
              :modelValue="filter.izin"
              :options="mIzins"
              placeholder="Pilih Jenis"
              :reduce="(mIzin) => mIzin.kode"
              label="nama"
              @update:modelValue="onFilterIzinFn"
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
      </form>
    </BCardBody>
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
        :line-numbers="true"
        :isLoading="isLoading"
        theme="polar-bear"
        styleClass="vgt-table striped sticky"
      >
        <template #table-row="props">
          <span v-if="props.column.field == 'mulai'">
            <h5 class="mb-1">{{ props.row.mulai_day_cast }}</h5>
            <span>
              {{ props.row.mulai_cast }}
            </span>
          </span>
          <span v-if="props.column.field == 'akhir'">
            <h5 class="mb-1">{{ props.row.akhir_day_cast }}</h5>
            <span>
              {{ props.row.akhir_cast }}
            </span>
          </span>
          <span v-if="props.column.field == 'periode'">
            {{ props.row.periode }} hari
          </span>
          <span v-if="props.column.field == 'nama'">
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
              <div class="p-2">
                <strong class="mb-1 d-block">{{ props.row?.nama }}</strong>
                <div class="badge badge-gradient-info">
                  {{ props.row.pemohon?.unit }}
                </div>
              </div>
            </div>
          </span>
          <span v-if="props.column.field === 'acc_status'">
            <div class="fs-14">
              <span
                v-if="props.row.acc_status == 0 || props.row.acc_status == null"
                class="badge badge-gradient-danger"
              >
                PENGAJUAN
              </span>
              <span
                v-else-if="props.row.acc_status == 1"
                class="badge badge-gradient-dark"
              >
                PROGRESS
              </span>
              <span
                v-else-if="props.row.acc_status == 2"
                class="badge badge-gradient-success"
              >
                Selesai
              </span>
              <span
                v-else-if="props.row.acc_status == 3"
                class="badge badge-gradient-danger"
              >
                DITOLAK
              </span>
              <span
                v-else-if="props.row.acc_status == 4"
                class="badge badge-gradient-danger"
              >
                BATAL
              </span>
            </div>
          </span>
          <span v-if="props.column.field == 'action'">
            <router-link
              :to="{ name: 'HistoryIzinDetail', params: { id: props.row.id } }"
              v-b-tooltip="'Lihat Detail'"
              class="btn btn-sm btn-soft-primary me-1"
            >
              <i class="ri-play-line"></i>
            </router-link>
            <BButton
              v-if="isSuperAdmin && props.row.status === SELESAI"
              variant="soft-danger"
              size="sm"
              v-b-tooltip="'Batal Izin'"
              class="me-1 mb-1"
              @click.prevent="batalIzinAct(props.row)"
            >
              <i class="ri-close-circle-line"></i>
            </BButton>
            <div v-if="props.row?.izin_bukti !== null">
              <button
                class="btn btn-sm btn-soft-warning waves-effect waves-light mb-1 me-1"
                @click.prevent="showBukti('.img-' + props.row.id)"
                v-b-tooltip="'Lihat Bukti'"
              >
                <i class="ri-image-2-fill"></i>
              </button>
              <div
                class="images d-none"
                :class="'img-' + props.row.id"
                v-viewer="{ movable: false }"
              >
                <img :src="getBukti(props.row.id)" />
              </div>
            </div>
          </span>
        </template>
      </vue-good-table>
    </div>
    <keterangan ref="keteranganRef" />
    <AccBy ref="accByRef" />
    <BatalIzin ref="batalIzinRef" @fetch="fetchData" />
  </BCard>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  historyIzinMethods,
  historyIzinState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import queryString from "query-string";
import { SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { IZIN_CUTI, IZIN_KRS } from "@/helpers/utils.js";
import { historyIzinService } from "@/services/HistoryIzinService";
import { webUrl } from "@/config/http";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import Keterangan from "@/views/izinku/Keterangan";
import AccBy from "@/views/history/AccBy";
import { mIzinService } from "@/services/MIzinService";
import flatPickr from "vue-flatpickr-component";
import BatalIzin from "./BatalIzin.vue";

export default {
  components: {
    VueGoodTable,
    Keterangan,
    AccBy,
    flatPickr,
    BatalIzin,
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
          label: "Nama",
          field: "nama",
        },
        {
          label: "Izin",
          field: "izin",
        },
        {
          label: "Mulai",
          field: "mulai",
        },
        {
          label: "Akhir",
          field: "akhir",
        },
        {
          label: "Periode",
          field: "periode",
        },
        {
          label: "Status",
          field: "acc_status",
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      totalRecords: 0,
      isLoading: false,
      jadwalku: null,
      listShift: [],
      user,
      unitList: [],
      mIzins: [],
      IZIN_CUTI,
      IZIN_KRS,
      SELESAI: 2,
    };
  },
  computed: {
    ...historyIzinState,
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  created() {
    this.fetchData();
    this.getUnitList();
    this.getMIzins();
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
    ...historyIzinMethods,
    async fetchData() {
      this.isLoading = true;
      const query = queryString.stringify(
        Object.assign({}, this.server, this.filter),
        {
          arrayFormat: "index",
        }
      );

      const [err, resp] = await historyIzinService.all(query);
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
    async getMIzins() {
      this.show();
      const [err, resp] = await mIzinService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.mIzins = resp.data;
      this.hide();
    },
    getBukti(id) {
      return `${webUrl}/izin/bukti/${id}`;
    },
    showBukti(params) {
      // const viewer = params.$viewer;
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    showKeterangan(params) {
      this.$refs.keteranganRef.title = params.title;
      this.$refs.keteranganRef.ket = params.ket;
      this.$refs.keteranganRef.showModal();
    },
    showAccBy({ accBy }) {
      this.$refs.accByRef.title = "Izin";
      this.$refs.accByRef.accBy = accBy;
      this.$refs.accByRef.showModal();
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
    onFilterIzinFn(val) {
      this.onFilterIzin(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    batalIzinAct(row) {
      let modal = this.$refs.batalIzinRef;
      modal.title = "Mulai Izin: " + row.mulai_day_cast + ", " + row.mulai_cast;
      modal.form.id = row.id;
      modal.showModal();
    },
    detailIzin(id) {
      let modal = this.$refs.listDetailRef;
      modal.form.id = id;
      modal.showModal();
    },
  },
};
</script>
