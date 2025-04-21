<template>
  <div>
    <div class="mb-1">
      <h5 class="pb-1 d-inline-block border-bottom">Presensi Selesai</h5>
      <BForm>
        <BRow class="g-2">
          <BCol lg="3">
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
          <BCol v-if="filter.month > 0 && isSuperAdmin" lg="3">
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
    <div class="mb-1">
      <vue-good-table
        mode="local"
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: true,
        }"
        :search-options="{
          enabled: true,
          externalQuery: filter.search,
        }"
        :line-numbers="true"
        :isLoading="isLoading"
        theme="polar-bear"
        styleClass="vgt-table"
      >
        <template #table-row="props">
          <span v-if="props.column.field === 'acc1'">
            <strong v-if="props.row.acc1_at !== null" class="text-success">
              Diterima
              <i class="ri-checkbox-circle-fill"></i>
            </strong>
          </span>
          <span v-if="props.column.field === 'acc2'">
            <strong
              v-if="props.row.acc2 !== null && props.row.acc2_at !== null"
              class="text-success"
            >
              Diterima
              <i class="ri-checkbox-circle-fill"></i>
            </strong>
            <strong
              v-else-if="props.row.acc2 === null && props.row.acc2_at !== null"
              class="text-success"
            >
              Tidak ada Acc 2
            </strong>
            <strong v-else-if="props.row.acc2 === null" class="text-danger">
              Tidak ada Acc 2
            </strong>
          </span>
          <span v-if="props.column.field === 'acc3'">
            <strong
              v-if="props.row.acc3 !== null && props.row.acc3_at !== null"
              class="text-success"
            >
              Diterima
              <i class="ri-checkbox-circle-fill"></i>
            </strong>
            <strong v-else-if="props.row.acc3 === null" class="text-danger">
              Tidak ada Acc 3
            </strong>
          </span>
          <span v-if="props.column.field == 'tanggal_izin'">
            <span
              v-if="props.row.jenis_table === IZIN_CUTI"
              class="text-center"
            >
              <p class="m-0">
                {{ `(${props.row.izin_cuti?.mulai_cast})` }}
              </p>
              <div>-</div>
              <p class="m-0">
                {{ `(${props.row.izin_cuti?.akhir_cast})` }}
              </p>
            </span>
            <span
              v-else-if="props.row.jenis_table === IZIN_KRS"
              class="text-center"
            >
              <p class="m-0">
                {{ `(${props.row.izin_cuti?.mulai})` }}
              </p>
              <div>-</div>
              <p class="m-0">
                {{ `(${props.row.izin_cuti?.akhir})` }}
              </p>
            </span>
          </span>
          <span v-if="props.column.field === 'acc_at'">
            <strong v-if="props.row.acc_at !== null" class="text-success">
              Diterima
              <i class="ri-checkbox-circle-fill"></i>
            </strong>
          </span>
          <span v-if="props.column.field === 'acc_status'">
            <strong v-if="props.row.acc_status === '2'" class="text-success">
              OK
              <i class="ri-checkbox-circle-fill"></i>
            </strong>
          </span>
          <span v-if="props.column.field === 'bukti'">
            <strong v-if="props.row?.izin_bukti !== null" class="text-success">
              <button
                class="btn btn-soft-success waves-effect waves-light"
                @click.prevent="showBukti('.img-' + props.row.id)"
              >
                <i class="ri-image-2-fill"></i>
                Lihat
              </button>
              <div
                class="images d-none"
                :class="'img-' + props.row.id"
                v-viewer="{ movable: false }"
              >
                <img
                  v-for="src in getBukti(props.row.id)"
                  :key="src"
                  :src="src"
                />
              </div>
            </strong>
            <strong v-else>-</strong>
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
import { months, getYears, SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { IZIN_CUTI, IZIN_KRS } from "@/helpers/utils.js";
import { historyIzinService } from "@/services/HistoryIzinService";
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
          label: "Tanggal",
          field: "tanggal_cast",
        },
        {
          label: "Karyawan",
          field: "pemohon.nama",
        },
        {
          label: "Unit",
          field: "pemohon.m_unit.nama",
        },
        {
          label: "Izin",
          field: "izin",
        },
        {
          label: "Mulai-Akhir",
          field: "tanggal_izin",
        },
        {
          label: "Status",
          field: "acc_status",
        },
        {
          label: "Bukti",
          field: "bukti",
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
    async fetchData() {
      this.isLoading = true;
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await historyIzinService.all(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.rows = resp.data;
      this.isLoading = false;
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
  },
};
</script>
