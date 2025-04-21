<template>
  <div class="p-2">
    <BRow class="g-2 align-items-end">
      <div class="col-lg-3">
        <label for="tanggal" class="form-label">
          Tanggal
          <span class="text-danger">*</span>
        </label>
        <flat-pickr
          v-model="filter.tanggal"
          placeholder="Pilih Tanggal"
          :config="{
            dateFormat: 'd-m-Y',
          }"
          class="form-control bg-light border-light"
          required
        ></flat-pickr>
      </div>
      <div class="col-lg">
        <BButton @click.prevent="onExport" variant="outline-success">
          <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
          Export
        </BButton>
      </div>
    </BRow>
  </div>
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { rekapService } from "@/services/RekapService";
import queryString from "query-string";

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      filter: {
        tanggal: null,
      },
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async onExport() {
      if (this.filter.tanggal === "") {
        this.toastError({
          title: "Gagal",
          msg: "Silahkan pilih Tanggal",
        });
        return;
      }
      this.show();
      const query = queryString.stringify(this.filter, {
        arrayFormat: "index",
      });
      const [err, response] = await rekapService.harian(query);
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
