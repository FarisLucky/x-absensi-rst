<template>
  <BCard no-body class="mb-1">
    <BCardHeader>
      <form autocomplete="off">
        <BRow class="justify-content-between align-items-center">
          <BCol cols="6">
            <span class="fs-14 font-weight">Belum Absen Hari ini</span>
          </BCol>
          <BCol cols="6">
            <div class="text-end">
              <v-select
                v-model="filter.shift"
                :options="['PAGI', 'SIANG', 'MALAM']"
                placeholder="Pilih Shift"
                @option:selected="fetchData"
                class="d-inline-block w-75"
              ></v-select>
            </div>
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
              style="border: 1px solid rgba(0, 0, 0, 0.2)"
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
        :isLoading="isLoading"
        @search="fetchData"
        theme="polar-bear"
        styleClass="vgt-table fs-10"
      >
        <template #table-row="props">
          <div v-if="props.column.field == 'nama'" class="d-flex">
            <div v-if="props.row.photo !== null" class="me-1">
              <BLink @click.prevent="showBukti('.img-' + props.row.nip)">
                <img
                  :src="getProfil(props.row?.nip)"
                  alt="karyawan-img"
                  class="avatar-sm rounded material-shadow img-fluid"
                  lazy
                />
              </BLink>
              <div
                class="images d-none"
                :class="'img-' + props.row.nip"
                v-viewer="{ movable: false }"
              >
                <img
                  :src="getProfil(props.row.nip)"
                  class="avatar-sm me-1 rounded material-shadow"
                  lazy
                />
              </div>
            </div>
            <img
              v-else
              src="@/assets/images/profil.jpg"
              class="avatar-sm me-1 rounded material-shadow"
            />
            <div class="p-1">
              <strong class="mb-1 d-block fs-11">{{ props.row.nama }}</strong>
              <span class="badge badge-gradient-info fs-9">
                {{ props.row.nama_unit }}
              </span>
            </div>
          </div>
          <span v-if="props.column.field == 'status'">
            <span
              v-if="props.row.status === null && props.row.libur == 1"
              class="fs-11"
            >
              Libur
            </span>
            <span v-else-if="props.row.status === null" class="fs-10">
              Belum Absen
            </span>
          </span>
        </template>
      </vue-good-table>
    </BCardBody>
  </BCard>
</template>
<script>
import { webUrl } from "@/config/http";
import { dashboardService } from "@/services/DashboardService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

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
          label: "Shift",
          field: "kode_shift",
        },
        {
          label: "Status",
          field: "status",
          tdClass: this.tdClassFunc,
        },
      ],
      rows: [],
      isLoading: false,
      filter: {
        shift: "PAGI",
        nama: "",
      },
    };
  },
  created() {
    this.fetchData();
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    tdClassFunc() {
      return "text-muted bg-light bg-opacity-25";
    },
    async fetchData() {
      this.isLoading = true;
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      const [err, resp] = await dashboardService.tablePresensiHarian(query);
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
    showBukti(params) {
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    onReset() {
      this.filter = {
        shift: "PAGI",
        nama: "",
      };
      this.fetchData();
    },
  },
};
</script>
<style scoped>
.vgt-table.polar-bear th {
  background-color: rgba(224, 242, 241, 0.5);
}
</style>
