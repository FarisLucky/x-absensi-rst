<template>
  <div class="p-2">
    <form autocomplete="off">
      <BRow class="g-2 align-items-end">
        <BCol cols="12">
          <span>Filter Berdasarkan</span>
        </BCol>
        <BCol cols="6" md="2">
          <label for="tanggal" class="form-label">
            Jenis
            <span class="text-danger">*</span>
          </label>
          <v-select
            :options="['BULANAN', 'TAHUNAN']"
            v-model="filter.jenis"
          ></v-select>
        </BCol>
        <BCol v-if="filter.jenis === 'BULANAN'" lg="3">
          <label class="mb-1">Range</label>
          <flat-pickr
            v-model="filter.range"
            placeholder="Pilih Range"
            :config="flatConfig"
            class="form-control bg-light border-light"
            required
          ></flat-pickr>
        </BCol>
        <BCol v-if="filter.jenis === 'TAHUNAN'" lg="2">
          <label class="mb-1">Mulai</label>
          <v-select :options="yearList" v-model="filter.range[0]"></v-select>
        </BCol>
        <BCol v-if="filter.jenis === 'TAHUNAN'" lg="2">
          <label class="mb-1">Akhir</label>
          <v-select :options="yearList" v-model="filter.range[1]"></v-select>
        </BCol>
        <BCol md="4">
          <label class="mb-1">Unit</label>
          <v-select
            v-model="filter.unit"
            :options="unitList"
            :reduce="(unit) => unit.id"
            label="nama"
            placeholder="Pilih Unit"
            multiple
          ></v-select>
        </BCol>
        <div class="col-lg">
          <BButton variant="outline-success" @click.prevent="onExport">
            <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
            Export
          </BButton>
        </div>
      </BRow>
    </form>
  </div>
</template>
<script>
import { webUrl } from "@/config/http";
import { mUnitService } from "@/services/MUnitService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import flatPickr from "vue-flatpickr-component";
import monthSelect from "flatpickr/dist/plugins/monthSelect";
import "flatpickr/dist/plugins/monthSelect/style.css";
import { getYears } from "@/helpers/utils";
import { rekapService } from "@/services/RekapService";

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      filter: {
        range: [],
        jenis: "BULANAN",
        unit: null,
      },
      webUrl,
      unitList: [],
      flatConfig: {
        mode: "range",
        plugins: [
          new monthSelect({
            shorthand: true, //defaults to false
            dateFormat: "Y-m", //defaults to "F Y"
            altFormat: "F Y", //defaults to "F Y"
          }),
        ],
      },
      yearList: [],
    };
  },
  watch: {
    "filter.jenis"(newValue) {
      this.filter.jenis = newValue;
      this.filter.range = [];
    },
  },
  created() {
    this.getUnit();
    this.yearList = getYears();
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
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
      this.unitList = resp.data;
      this.unitList.unshift({
        id: -1,
        nama: "SEMUA",
      });
      this.hide();
    },
    async onExport() {
      if (
        this.filter.range === "" ||
        this.filter.unit === "" ||
        this.filter.jenis === ""
      ) {
        this.toastError({
          title: "Gagal",
          msg: "Range tidak boleh kosong",
        });
        return;
      }
      this.show();
      const query = queryString.stringify(this.filter, {
        arrayFormat: "index",
      });
      const [err, response] = await rekapService.presensiBulanan(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        console.error("Error downloading Excel file:", err);
        this.hide();

        return;
      }

      // Create a link element
      const url = window.URL.createObjectURL(new Blob([response]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute(
        "download",
        `rekap-presensi-${this.filter.tanggal}.xlsx`
      ); // Set the file name
      document.body.appendChild(link);
      link.click();

      // Clean up
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
      this.hide();
    },
  },
};
</script>
