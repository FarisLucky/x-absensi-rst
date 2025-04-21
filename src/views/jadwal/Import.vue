<template>
  <BModal
    v-model="modal"
    id="importJadwal"
    modal-class="zoomIn"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    size="lg"
    title="Import Jadwal"
  >
    <form @submit.prevent="onSubmit" autocomplete="off">
      <BRow class="g-2 align-items-end">
        <BCol md="2">
          <label for="year">Tahun</label>
          <v-select
            v-model="filter.year"
            :options="years"
            placeholder="Pilih Tahun"
            id="year"
          ></v-select>
        </BCol>
        <BCol md="2">
          <label for="month">Month</label>
          <v-select
            v-model="filter.month"
            :options="months"
            :reduce="(l) => l.id"
            label="name"
            placeholder="Pilih Bulan"
            id="month"
          ></v-select>
        </BCol>
        <BCol>
          <v-select
            v-model="filter.idUnit"
            :options="units"
            :reduce="(unit) => unit.id"
            placeholder="Pilih Unit"
            label="nama"
            multiple
          ></v-select>
        </BCol>
        <BCol cols="12">
          <div class="text-end">
            <BButton
              @click.prevent="onReset"
              class="btn btn-soft-secondary me-1"
            >
              <i class="ri-refresh-line"></i>
            </BButton>
            <BButton
              type="button"
              @click.prevent="onDownload"
              class="btn btn-outline-danger"
              >Download Template</BButton
            >
          </div>
        </BCol>
      </BRow>
      <br />
      <BCol lg="12">
        <label for="import" class="form-label">
          Import

          <small class="text-danger"
            >(Gunakan template diatas untuk melakukan import jadwal)</small
          >
        </label>
        <input
          type="file"
          name="file"
          id="import"
          class="form-control"
          ref="fileRef"
          placeholder="Silahkan pilih file yang diupload"
        />
      </BCol>
      <BCol lg="12">
        <div class="mb-1 text-end">
          <BButton type="submit" variant="primary" :disabled="progress">
            {{ progress ? "Tunggu Dulu" : "Simpan" }}
          </BButton>
        </div>
      </BCol>
    </form>
  </BModal>
</template>
<script>
import { webUrl } from "@/config/http";
import { jadwalService } from "@/services/JadwalService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { months, getYears } from "@/helpers/utils";
import queryString from "query-string";

export default {
  props: ["units"],
  data() {
    const user = this.$store.state.auth.data;
    return {
      modal: false,
      file: null,
      progress: false,
      webUrl,
      filter: {
        month: "",
        year: new Date().getFullYear(),
        idUnit: [],
      },
      months,
      years: [],
      user,
    };
  },
  created() {
    this.years = getYears();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
      this.onReset();
    },
    async onSubmit() {
      if (this.filter.year === "" || this.filter.month === "") {
        this.toastError({
          title: "Gagal",
          msg: "Silahkan pilih Tahun dan Bulan",
        });
        return;
      }
      if (this.$refs.fileRef.files.length === 0) {
        this.toastError({
          title: "Gagal",
          msg: "Silahkan pilih File",
        });
        return;
      }
      this.progress = true;
      this.show();
      let formData = new FormData();
      formData.append("file", this.$refs.fileRef.files[0]);
      formData.append("year", this.filter.year);
      formData.append("month", this.filter.month);

      const [err, resp] = await jadwalService.importExcel(formData);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.progress = false;
        this.hide();

        return;
      }
      this.progress = false;
      this.hideModal();
      this.hide();
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.emptyFile();
    },
    async onDownload() {
      if (
        this.filter.year === "" ||
        this.filter.month === "" ||
        this.filter.idUnit.length < 1
      ) {
        this.toastError({
          title: "Gagal",
          msg: "Silahkan pilih Tahun, Bulan dan Unit",
        });
        return;
      }
      this.show();
      const query = queryString.stringify(this.filter, {
        arrayFormat: "index",
      });
      const [err, response] = await jadwalService.downloadTemplate(query);
      if (err) {
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
        `template-${this.filter.month}/${this.filter.year}.xlsx`
      ); // Set the file name
      document.body.appendChild(link);
      link.click();

      // Clean up
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
      this.hide();
    },
    emptyFile() {
      this.$refs.fileRef.value = null;
    },
    onReset() {
      this.filter = {
        month: "",
        year: new Date().getFullYear(),
        idUnit: [],
      };
      this.emptyFile();
    },
  },
};
</script>
