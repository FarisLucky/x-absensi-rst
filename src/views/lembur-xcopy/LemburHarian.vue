<template>
  <BCard no-body>
    <BCardHeader>
      <BCardTitle class="mb-1"> Harian Lembur </BCardTitle>
    </BCardHeader>
    <BCardBody>
      <div class="mb-1">
        <BForm
          ><BRow class="g-2">
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
                :modelValue="filter.tanggal"
                @update:modelValue="onFilterTanggalFn"
                placeholder="Pilih Tanggal"
                :config="{
                  wrap: true, // set wrap to true only when using 'input-group'
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
            <BCol :lg="2">
              <v-select
                :modelValue="filter.jenis"
                :options="jenisList"
                placeholder="Pilih Jenis"
                :reduce="(jenis) => jenis.id"
                label="nama"
                @update:modelValue="onFilterJenisFn"
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
          :isLoading="isLoading"
          :line-numbers="true"
          :sort-options="{
            enabled: false,
          }"
          theme="polar-bear"
          styleClass="vgt-table striped sticky"
        >
          <template #table-row="props">
            <span v-if="props.column.field == 'absen'">
              <div class="bg-info-subtle text-info p-1 rounded d-inline-block">
                {{ props.row.absen }}
              </div>
            </span>
            <span v-if="props.column.field == 'acc_status'">
              <div
                class="bg-secondary-subtle text-secondary p-1 rounded d-inline-block"
              >
                <span
                  v-if="
                    props.row.masuk == null && props.row.absen != 'BACKDATE'
                  "
                  >BELUM ABSEN</span
                >
                <span
                  v-else-if="
                    props.row.masuk !== null &&
                    props.row.keluar == null &&
                    props.row.absen == 'GPS'
                  "
                  >SEDANG LEMBUR</span
                >
                <span
                  v-else-if="
                    props.row.masuk !== null &&
                    props.row.keluar !== null &&
                    props.row.absen == 'GPS'
                  "
                  >SELESAI</span
                >
                <span
                  v-else-if="
                    props.row.masuk !== null && props.row.absen == 'FOTO'
                  "
                >
                  Selesai
                  <BLink
                    @click.prevent="showBukti('.img' + props.row.id)"
                    target="__blank"
                  >
                    <i class="ri-image-2-fill"></i>
                    Lihat
                  </BLink>
                  <div
                    class="images d-none"
                    :class="'img-' + props.row?.id"
                    v-viewer="{ movable: false }"
                  >
                    <img
                      v-for="src in [props.row.bukti_url_cast]"
                      :key="src"
                      :src="src"
                    />
                  </div>
                </span>
                <span v-else-if="props.row.absen == 'BACKDATE'">SELESAI</span>
              </div>
            </span>
          </template>
        </vue-good-table>
      </div>
    </BCardBody>
  </BCard>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  lemburHarianMethods,
  lemburHarianState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import queryString from "query-string";
import { lemburService } from "@/services/LemburService";
import flatPickr from "vue-flatpickr-component";
import {
  SUPER_ADMIN,
  KASUB,
  KABID,
  DIREKTUR,
  STAF,
  KEPALA,
} from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { mLemburService } from "@/services/MLemburService";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

export default {
  components: {
    flatPickr,
    VueGoodTable,
  },
  data() {
    const user = this.$store.state?.auth?.data;

    return {
      columns: [
        {
          label: "Unit",
          field: "m_unit.nama",
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Lembur",
          field: "lembur",
        },
        {
          label: "Tanggal",
          field: "tanggal_cast",
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
          label: "Ket",
          field: "ket",
        },
        {
          label: "Absen",
          field: "absen",
        },
        {
          label: "Status",
          field: "acc_status",
        },
      ],
      rows: [],
      totalRecords: 0,
      isLoading: false,
      user,
      jenisList: [],
      unitList: [],
    };
  },
  created() {
    this.fetchData();
    this.getUnitList();
    this.getJenisList();
  },
  computed: {
    ...lemburHarianState,
    isStaf() {
      return this.$store.state.auth.data.role === STAF;
    },
    isKepala() {
      return this.$store.state.auth.data.role === KEPALA;
    },
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
    ...lemburHarianMethods,
    async fetchData() {
      this.isLoading = true;

      const query = queryString.stringify(
        Object.assign({}, this.server, this.filter),
        {
          arrayFormat: "index",
        }
      );
      const [err, resp] = await lemburService.all(query);
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
    async getJenisList() {
      this.show();
      const [err, resp] = await mLemburService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.jenisList = resp.data;
      this.hide();
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
    onFilterJenisFn(val) {
      this.onFilterJenis(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    showBukti(params) {
      // const viewer = params.$viewer;
      console.log(params);
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
  },
};
</script>
