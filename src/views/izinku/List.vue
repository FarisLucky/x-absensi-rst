<template>
  <BRow>
    <BCol xl="12">
      <div class="mb-3">
        <form autocomplete="off">
          <BRow class="g-2">
            <BCol lg="3">
              <div class="search-box">
                <input
                  type="text"
                  class="form-control search bg-light border-light"
                  placeholder="Cari disini..."
                  v-model="filter.search"
                  @keyup.enter="fetchData"
                />
                <i class="ri-search-line search-icon"></i>
              </div>
            </BCol>
            <BCol cols="6" md="2">
              <v-select
                v-model="filter.year"
                :options="years"
                placeholder="Pilih Tahun"
              ></v-select>
            </BCol>
            <BCol v-if="filter.year > 0" cols="6" md="2">
              <v-select
                v-model="filter.month"
                :options="months"
                :reduce="(l) => l.id"
                label="name"
                placeholder="Pilih Bulan"
              ></v-select>
            </BCol>
            <BCol v-if="filter.year > 0" cols="6" md="3">
              <v-select
                v-model="filter.izin"
                :options="mIzins"
                :reduce="(l) => l.id"
                label="nama"
                placeholder="Pilih Izin"
              ></v-select>
            </BCol>
            <BCol>
              <button
                type="submit"
                class="btn btn-success me-1"
                @click.prevent="fetchData"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
                Cari
              </button>
              <BButton
                type="button"
                variant="outline-secondary"
                @click="onRefresh"
                class="me-1"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
                Reset
              </BButton>
            </BCol>
          </BRow>
        </form>
      </div>
      <BRow v-if="rows.length > 0" class="g-2">
        <BCol v-for="(list, idx) in rows" :key="idx" md="8">
          <div class="p-3 border rounded">
            <div class="d-flex justify-content-between">
              <div class="flex-shrink-0">
                <div
                  class="d-flex gap-2 align-items-center justify-content-center"
                >
                  <div>
                    <img
                      v-if="list.pemohon?.photo !== null"
                      :src="list.pemohon?.photo_url_cast"
                      class="avatar-sm me-1 rounded material-shadow"
                    />
                    <img
                      v-else
                      src="@/assets/images/profil.jpg"
                      class="avatar-sm me-1 rounded material-shadow"
                      width="30px"
                    />
                  </div>
                  <div class="p-2">
                    <h5 class="mb-0 d-block fs-13">
                      {{ list.nama }}

                      <i
                        class="ri-information-line text-danger"
                        v-b-tooltip="`Unit: ${list.unit}`"
                        style="cursor: pointer"
                      ></i>
                    </h5>
                    <span class="text-muted fs-11">
                      {{ list.izin }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex-shrink-0">
                <span class="badge badge-gradient-success">{{
                  list.acc_status_desc
                }}</span>
              </div>
            </div>
            <div class="mt-2 mb-3">
              <div class="py-1">
                <i class="ri-calendar-line me-2 fs-13"></i>
                <span class="fs-12">
                  Tanggal:
                  <span class="text-muted">{{
                    `${list.mulai_cast} - ${list.akhir_cast}`
                  }}</span>
                </span>
              </div>
              <div class="py-1">
                <i class="ri-time-line me-2 fs-13"></i>
                <span class="fs-12">
                  Periode:
                  <span class="text-muted">{{ ` ${list.periode} hari` }}</span>
                </span>
              </div>
              <div class="py-1">
                <i class="ri-file-list-2-line me-2 fs-13"></i>
                <span class="mb-0 fs-12">
                  Alasan:
                  <span class="text-muted">{{ list.ket }}</span>
                </span>
              </div>
              <!-- <div class="mt-2">
                <h5 class="mb-0 fs-14">{{ list.ket }}</h5>
              </div> -->
            </div>
            <hr />
            <BAccordion
              class="custom-accordionwithicon"
              flush
              id="accordionFlushExample"
            >
              <BAccordionItem headerClass="p-0" buttonClass="py-2 px-0">
                <template #title>
                  <i class="ri-time-line me-2"></i>
                  Dibuat pada: {{ `${list.created_at_cast}` }}
                </template>
                <BRow>
                  <BCol cols="6">
                    <ul style="list-style-type: none">
                      <li>Status</li>
                      <li>Disetujui Oleh</li>
                      <li>Tanggal Persetujuan</li>
                      <li>Diajukan Pada</li>
                      <li>Alasan</li>
                    </ul>
                  </BCol>
                  <BCol cols="6">
                    <ul style="list-style-type: none">
                      <li>{{ list.acc_status_desc }}</li>
                      <li>{{ list.acc_nama }}</li>
                      <li>{{ list.acc_at_cast }}</li>
                      <li>{{ list.created_at_cast }}</li>
                      <li>{{ list.ket }}</li>
                    </ul>
                  </BCol>
                </BRow>
              </BAccordionItem>
            </BAccordion>
          </div>
        </BCol>
      </BRow>
      <h4 v-else class="py-4 text-center border rounded">Tidak ada Data</h4>
    </BCol>
    <Keterangan ref="keteranganRef" />
  </BRow>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { ICT, IZIN_CUTI, IZIN_KRS } from "@/helpers/utils.js";
import { months, getYears } from "@/helpers/utils";
import { izinService } from "@/services/IzinService";
import queryString from "query-string";
import { webUrl } from "@/config/http";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { defineAsyncComponent } from "vue";
import { mIzinService } from "@/services/MIzinService";

const initFilter = () => {
  const currentTime = new Date();

  return {
    search: "",
    bawahan: "unit",
    month: currentTime.getMonth() + 1,
    year: currentTime.getFullYear(),
    izin: "",
  };
};

export default {
  components: {
    Keterangan: defineAsyncComponent(() => import("@/views/izinku/Keterangan")),
  },
  data() {
    const user = this.$store.state?.auth?.data;

    return {
      filter: initFilter(),
      columns: [
        {
          label: "Nama",
          field: "izin",
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
          label: "Jml",
          field: "periode",
        },
        {
          label: "Sisa",
          field: "sisa",
        },
        {
          label: "Ket",
          field: "ket",
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
      user,
      months,
      years: [],
      mIzins: [],
      IZIN_CUTI,
      IZIN_KRS,
      ICT,
    };
  },
  created() {
    this.fetchData();
    this.getMIzins();
    this.years = getYears();
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
      this.show();

      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      const [err, resp] = await izinService.selesai(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.rows = resp.data;
      this.hide();
    },
    async getMIzins() {
      this.show();
      const [err, resp] = await mIzinService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.mIzins = resp.data;
      this.hide();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
    },
    getBukti(id) {
      let bukti = [];
      let file = `${webUrl}/izin/bukti/${id}`;

      bukti.push(file);

      return bukti;
    },
    showImg(params) {
      // const viewer = params.$viewer;
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
    showKeterangan(params) {
      console.log(params);
      this.$refs.keteranganRef.title = params.title;
      this.$refs.keteranganRef.ket = params.ket;
      this.$refs.keteranganRef.showModal();
    },
  },
};
</script>
