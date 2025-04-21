<style scoped>
.vgt-table.polar-bear tr td:first-child() {
  position: sticky;
  left: 0;
}
</style>
<template>
  <BModal
    v-model="modal"
    id="lihatJadwal"
    modal-class="zoomIn"
    hide-footer
    class="v-modal-custom"
    centered
    fullscreen
    header-class="p-3 bg-info-subtle"
    title="Lihat Jadwal"
  >
    <BRow class="g-2">
      <BCol lg="12">
        <h5 class="fs-16 m-0 mb-3">
          <b>{{ form.unit }}</b>
        </h5>
      </BCol>
      <BCol lg="2">
        <v-select
          v-model="filter.year"
          :options="years"
          placeholder="Pilih Tahun"
          id="year"
        ></v-select>
      </BCol>
      <BCol v-if="filter.year > 0" lg="2">
        <v-select
          v-model="filter.month"
          :options="months"
          :reduce="(l) => l.id"
          label="name"
          placeholder="Pilih Bulan"
          id="month"
        ></v-select>
      </BCol>
      <BCol lg="3">
        <v-select
          v-model="filter.idUnit"
          :options="units"
          :reduce="(unit) => unit.id"
          @option:selected="onShowJadwal"
          placeholder="Pilih Unit"
          label="nama"
        ></v-select>
      </BCol>
      <BCol>
        <BButton
          type="submit"
          class="me-1"
          variant="primary"
          :disabled="progress"
          @click.prevent="onShowJadwal"
        >
          <i class="ri-search-line"></i>
          {{ progress ? "Tunggu Dulu" : "Tampil" }}
        </BButton>
        <BButton variant="soft-secondary" @click.prevent="reset">
          <i class="ri-refresh-line"></i>
          Reset
        </BButton>
      </BCol>
      <BCol lg="12">
        <div v-if="rows.length > 0">
          <simplebar id="scrollbar" class="w-100 h-100" ref="scrollbar">
            <div class="table-responsive">
              <vue-good-table
                mode="local"
                :columns="columns"
                :rows="rows"
                :sort-options="{
                  enabled: false,
                }"
                :select-options="{
                  enabled: true,
                  selectOnCheckboxOnly: true, // only select when checkbox is clicked instead of the row
                  selectionInfoClass: 'bg-warning-subtle',
                  selectionText: 'karyawan dipilih',
                  clearSelectionText: 'hapus',
                }"
                theme="polar-bear"
                styleClass="vgt-table bordered sticky"
                ref="showJadwalTable"
              >
              </vue-good-table>
            </div>
          </simplebar>
        </div>
        <div v-else>
          <h4 class="fs-18 py-5 text-center rounded border">
            Tidak ada Jadwal <br />
            <span class="mt-1">
              {{ `Tahun ${this.filter.year} Bulan ${this.filter.month}` }}
            </span>
          </h4>
        </div>
      </BCol>
    </BRow>
  </BModal>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { months, SUPER_ADMIN } from "@/helpers/utils";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { jadwalService } from "@/services/JadwalService";
import queryString from "query-string";
import simplebar from "simplebar-vue";

const formInit = () => ({
  unit: "",
});

export default {
  components: {
    VueGoodTable,
    simplebar,
  },
  props: ["units"],
  data() {
    const user = this.$store.state.auth.data;
    const currentTime = new Date();

    return {
      form: formInit(),
      modal: false,
      progress: false,
      progressReset: false,
      filter: {
        month: currentTime.getMonth() + 1,
        year: currentTime.getFullYear(),
        idUnit: user?.id_unit,
      },
      months,
      years: [],
      user,
      columns: [],
      rows: [],
    };
  },
  created() {
    this.years = this.generateYear();
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
      this.rows = [];
      this.columns = [];
    },
    async onShowJadwal() {
      if (
        this.filter.idUnit === "" ||
        this.filter.idUnit < 1 ||
        this.filter.idUnit === undefined
      ) {
        this.toastError({
          title: "Gagal",
          msg: "Pilih Unit Dulu",
        });
        return;
      }
      this.progress = true;
      this.rows = [];
      this.show();
      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      const [err, resp] = await jadwalService.showJadwalUnit(query);
      if (err) {
        this.progress = false;
        this.hide();

        return;
      }
      this.progress = false;
      this.columns = resp.data.columns.map((column) => {
        return {
          label: column.toString().toUpperCase(),
          field: column.toString(),
        };
      });
      this.rows = resp.data.rows;
      this.form.unit = resp.data.unit;
      this.hide();
    },
    async onKosongkanJadwal() {
      this.progressReset = true;
      // Show spinner
      this.rows = [];
      this.show();

      const query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const listNip = this.$refs.showJadwalTable.selectedRows.map((row) => {
        return row.nama.split("-")[0];
      });

      if (listNip.length < 1) {
        this.toastError({
          title: "Gagal",
          msg: "Pilih karyawan",
        });
        this.progressReset = false;
        // Hide spinner
        this.hide();

        return;
      }

      const [err, resp] = await jadwalService.kosongkanJadwal({
        query: query,
        list_nip: listNip,
      });

      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.progressReset = false;
        // Hide spinner
        this.hide();

        return;
      }
      this.toastSuccess({
        title: "Gagal",
        msg: resp.data,
      });
      this.progressReset = false;
      this.hide();
    },
    reset() {
      this.rows = [];
      this.columns = [];
      const user = this.$store.state.auth.data;
      const currentTime = new Date();
      this.filter = {
        month: currentTime.getMonth() + 1,
        year: currentTime.getFullYear(),
        idUnit: user?.id_unit,
      };
      this.onShowJadwal();
    },
    generateYear() {
      var dump = [];
      var max = new Date().getFullYear() + 1;
      var min = max - 1;

      for (var i = max; i >= min; i--) {
        dump.push(i);
      }

      return dump;
    },
  },
};
</script>
