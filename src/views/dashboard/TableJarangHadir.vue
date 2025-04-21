<template>
  <BCard>
    <div class="d-flex align-items-center justify-content-between">
      <MonthPickerInput
        lang="id"
        @input="onFilterMonth"
        placeholder="Pilih Bulan"
        :clearable="true"
        :default-month="new Date().getMonth() + 1"
        :default-year="new Date().getFullYear()"
      ></MonthPickerInput>
      <h4>Kehadiran Rendah</h4>
    </div>
    <div class="mt-2">
      <vue-good-table
        mode="local"
        :columns="columns"
        :rows="rows"
        :select-options="{
          enabled: true,
          selectOnCheckboxOnly: true,
        }"
        :pagination-options="{
          enabled: true,
        }"
        :line-numbers="true"
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
                {{ props.row.unit }}
              </span>
            </div>
          </div>
        </template>
      </vue-good-table>
    </div>
  </BCard>
</template>
<script>
import { webUrl } from "@/config/http";
import { dashboardService } from "@/services/DashboardService";
import queryString from "query-string";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { MonthPickerInput } from "vue-month-picker";

export default {
  components: {
    VueGoodTable,
    MonthPickerInput,
  },
  data() {
    return {
      isLoading: false,
      rows: [],
      columns: [
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Total Alfa (Bulan Ini)",
          field: "ttl",
        },
      ],
      filter: {
        month: new Date(),
        jenis: "terendah",
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.isLoading = true;

      let params = {
        month: `${this.filter.month.getFullYear()}-${
          this.filter.month.getMonth() + 1
        }`,
        jenis: this.filter.jenis,
      };
      const query = queryString.stringify(Object.assign({}, params), {
        arrayFormat: "index",
      });
      const [err, resp] = await dashboardService.listPresensiTerendah(query);
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
    onFilterMonth(e) {
      this.filter.month = new Date(e.year, e.monthIndex - 1, 1);
      this.fetchData();
    },
  },
};
</script>
