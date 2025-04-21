<template>
  <BCard no-body class="mb-1">
    <BCardHeader>
      <form autocomplete="off" class="my-2">
        <BRow class="justify-content-between align-items-center">
          <BCol cols="6">
            <span class="fs-14 font-weight">Izin Hari ini</span>
          </BCol>
        </BRow>
      </form>
    </BCardHeader>
    <BCardBody class="bg-cust">
      <form autocomplete="off" @submit.prevent="fetchData">
        <div class="input-group mb-2">
          <div class="search-box">
            <input
              type="text"
              class="form-control"
              v-model="filter.nama"
              placeholder="Cari Nama"
            />
            <i class="ri-search-line search-icon"></i>
          </div>
          <button class="btn btn-primary" type="submit">
            <i class="ri-file-search-line"></i>
            Cari
          </button>
          <button
            class="btn btn-secondary"
            type="reset"
            @click.prevent="onReset"
          >
            <i class="ri-refresh-line"></i>
          </button>
        </div>
      </form>
      <vue-good-table
        mode="local"
        :columns="columns"
        :rows="rows"
        :pagination-options="{
          enabled: true,
          perPage: 4,
        }"
        :sort-options="{
          enabled: false,
        }"
        :search-options="{
          enabled: true,
          externalQuery: filter.nama,
        }"
        :isLoading="isLoading"
        theme="polar-bear"
        styleClass="vgt-table"
      >
        <template #table-row="props">
          <div v-if="props.column.field == 'nama'" class="d-flex">
            <img
              v-if="props.row.photo !== null"
              :src="getProfil(props.row.nip)"
              class="avatar-sm me-1 rounded material-shadow"
              lazy
            />
            <img
              v-else
              src="@/assets/images/profil.jpg"
              class="avatar-sm me-1 rounded material-shadow"
              lazy
            />
            <div class="p-1">
              <strong class="mb-1 d-block fs-11">{{ props.row.nama }}</strong>
              <span class="badge badge-gradient-info fs-9">
                {{ props.row.pemohon?.m_unit?.nama }}
              </span>
            </div>
          </div>
          <span v-if="props.column.field == 'kode_izin'">
            <strong class="d-block">
              {{ props.row.kode_izin }}
            </strong>
            <small v-if="props.row.jenis_table === IZIN_CUTI">
              {{ props.row.izin_cuti?.mulai_cast }}
            </small>
            <small v-else-if="props.row.jenis_table === IZIN_KRS">
              {{ props.row.izin_krs?.mulai }}
            </small>
            /
            <small v-if="props.row.jenis_table === IZIN_CUTI">
              {{ props.row.izin_cuti?.akhir_cast }}
            </small>
            <small v-else-if="props.row.jenis_table === IZIN_KRS">
              {{ props.row.izin_krs?.akhir ?? "-" }}
            </small>
          </span>
        </template>
      </vue-good-table>
    </BCardBody>
  </BCard>
</template>
<script>
import { dashboardService } from "@/services/DashboardService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { IZIN_CUTI, IZIN_KRS } from "@/helpers/utils.js";
import { webUrl } from "@/config/http";

export default {
  components: {
    VueGoodTable,
  },
  data() {
    return {
      columns: [
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Izin",
          field: "kode_izin",
        },
        {
          label: "Keterangan",
          field: "ket",
          tdClass: this.tdClassFunc,
        },
      ],
      isLoading: false,
      rows: [],
      IZIN_CUTI,
      IZIN_KRS,
      filter: {
        jenis_izin: "",
        nama: "",
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    tdClassFunc() {
      return "text-muted bg-light bg-opacity-25";
    },
    async fetchData() {
      this.isLoading = true;
      const [err, resp] = await dashboardService.tableIzinHarian();
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
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
    onReset() {
      this.filter = {
        jenis_izin: "",
        nama: "",
      };
      this.fetchData();
    },
  },
};
</script>
<style></style>
