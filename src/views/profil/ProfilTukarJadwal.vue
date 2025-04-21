<template>
  <div>
    <div class="p-2 mb-1">
      <BRow class="g-2 align-items-end">
        <BCol cols="6" md="3">
          <label>Filter</label>
          <flat-pickr
            v-model="filter.range"
            placeholder="Pilih Tanggal"
            :config="{
              mode: 'range',
              dateFormat: 'd-m-Y',
            }"
            class="form-control"
          ></flat-pickr>
        </BCol>
        <BCol>
          <BButton variant="info" @click.prevent="fetchData" class="me-1">
            <i class="ri-search-line"></i>
            Cari
          </BButton>
          <BButton variant="soft-secondary" @click.prevent="onReset">
            <i class="ri-refresh-fill"></i>
          </BButton>
        </BCol>
      </BRow>
    </div>
    <div class="p-2 mb-1">
      <BRow class="g-2">
        <BCol v-for="(tukar, idx) in statistiks" :key="idx" cols="6" md="3">
          <div class="p-3 rounded border">
            <p class="mb-1">{{ idx }}</p>
            <h5 class="fs-16">{{ tukar }}</h5>
          </div>
        </BCol>
      </BRow>
    </div>
    <div class="p-2">
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
        theme="polar-bear"
        styleClass="vgt-table striped"
      >
        <!-- <template #table-row="props">
          <span v-if="props.column.field === 'aksi'">
            <router-link
              :to="{ name: 'HistoryIzinDetail', params: { id: props.row.id } }"
              v-b-tooltip="'Lihat Detail'"
              class="btn btn-sm btn-soft-primary me-1"
            >
              <i class="ri-play-line"></i>
            </router-link>
          </span>
        </template> -->
      </vue-good-table>
    </div>
  </div>
</template>

<script>
import { IZIN_CUTI } from "@/helpers/utils";
import { profileService } from "@/services/ProfileService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import flatPickr from "vue-flatpickr-component";
import queryString from "query-string";

export default {
  components: {
    VueGoodTable,
    flatPickr,
  },
  data() {
    return {
      IZIN_CUTI,
      columns: [
        {
          label: "Izin",
          field: "izin",
        },
        {
          label: "Jadwal",
          field: "tgl_pihak1",
        },
        {
          label: "Permintaan",
          field: "tgl_pihak2",
        },
        {
          label: "Tukar Dengan",
          field: "nama_pihak2",
        },
        {
          label: "Keterangan",
          field: "ket",
        },
        {
          label: "Status",
          field: "acc_status_desc",
        },
        {
          label: "Aksi",
          field: "aksi",
        },
      ],
      rows: [],
      statistiks: [],
      filter: {
        range: "",
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async fetchData() {
      this.show();
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      const [err, resp] = await profileService.getTukar({
        nip: this.$route.params.nip,
        query: query,
      });
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.rows = resp.data?.list;
      this.statistiks = resp.data?.statistik;
      this.hide();
    },
    onReset() {
      this.filter = {
        range: "",
      };
      this.fetchData();
    },
  },
};
</script>
