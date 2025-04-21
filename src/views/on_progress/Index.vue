<template>
  <Layout>
    <PageHeader title="OnProgress" pageTitle="pages" />
    <div class="h-100">
      <BRow>
        <BCol xl="12">
          <BCard no-body>
            <BCardHeader class="border-0">
              <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">On Progress</h5>
              </div>
            </BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <BForm>
                <BRow class="g-2 align-items-end">
                  <BCol lg="2" sm="4">
                    <label>Nama</label>
                    <div class="search-box">
                      <input
                        type="text"
                        class="form-control search bg-light border-light"
                        placeholder="Cari Karyawan disini..."
                        :value="filter.search"
                        @input="onFilterSearchFn"
                      />
                      <i class="ri-search-line search-icon"></i>
                    </div>
                  </BCol>
                  <BCol lg="4">
                    <label>Unit</label>
                    <v-select
                      :modelValue="filter.unit"
                      :options="units"
                      :reduce="(l) => l.id"
                      label="nama"
                      placeholder="Pilih Unit"
                      @update:modelValue="onFilterUnitFn"
                    ></v-select>
                  </BCol>
                  <BCol lg="2">
                    <label>Status</label>
                    <v-select
                      :modelValue="filter.status"
                      :options="['TELAT', 'TEPAT', 'IZIN']"
                      placeholder="Pilih Status"
                      @update:modelValue="onFilterStatusFn"
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
                <small class="text-danger">minimal 3 karakter</small>
              </BForm>
            </BCardBody>
            <BCardBody>
              <div class="mb-1">
                <BRow v-if="listPresensi?.length > 0" class="g-1">
                  <BCol
                    xxl="3"
                    md="4"
                    v-for="(data, idx) in listPresensi"
                    :key="idx"
                  >
                    <BCard no-body class="mb-1">
                      <BCardBody class="p-1">
                        <div class="d-flex align-items-center overflow-hidden">
                          <div class="flex-shrink-0">
                            <div
                              v-if="data.m_karyawan?.photo !== null"
                              class="rounded-3 avatar-md border border-secondary"
                            >
                              <BLink
                                @click.prevent="showBukti('.img-' + data.nip)"
                              >
                                <img
                                  :src="getProfil(data.nip)"
                                  alt="karyawan-img"
                                  class="img-fluid img-fit"
                                  loading="lazy"
                                />
                              </BLink>
                              <div
                                class="images d-none"
                                :class="'img-' + data.nip"
                                v-viewer="{ movable: false }"
                              >
                                <img :src="data.m_karyawan.photo_url_cast" />
                              </div>
                            </div>
                            <div v-else class="avatar-md rounded">
                              <img
                                v-if="data.presensi?.m_karyawan?.sex === 'L'"
                                src="@/assets/images/male.png"
                                class="member-img img-fluid d-block rounded"
                                loading="lazy"
                              />
                              <img
                                v-else
                                src="@/assets/images/female.png"
                                class="member-img img-fluid d-block rounded"
                                loading="lazy"
                              />
                            </div>
                          </div>
                          <div class="flex-grow-1 ms-3">
                            <div
                              class="overflow-hidden mt-1"
                              style="height: 19px"
                            >
                              <h5 class="fs-14 mb-1 overflow-hidden">
                                {{ data.nama }}
                              </h5>
                            </div>
                            <div class="badge badge-gradient-info">
                              {{ data.unit ?? "-" }}
                            </div>
                            <div class="text-muted mt-1">
                              <i
                                class="ri-time-line text-primary me-1 align-bottom"
                              ></i>
                              <span>Masuk : </span>
                              {{ `${data?.tanggal_cast} ${data?.jam_masuk}` }}
                            </div>
                            <div class="text-muted">
                              <div class="mb-1">
                                <i
                                  class="ri-map-pin-2-line text-primary me-1 align-bottom"
                                ></i>
                                <span>Absen : </span>
                                {{
                                  `${data.tanggal_cast} (${
                                    data.presensi?.masuk ?? ""
                                  }-${data.presensi?.pulang ?? ""})`
                                }}
                              </div>
                              <div class="fs-14">
                                <span
                                  v-if="data.presensi !== null"
                                  class="badge"
                                  :class="{
                                    'bg-success-subtle text-dark':
                                      data.presensi?.status == 'TEPAT',
                                    'bg-danger-subtle text-dark':
                                      data.presensi?.status == 'TELAT',
                                  }"
                                >
                                  {{ data.presensi?.status }}
                                  <span
                                    v-if="data.presensi?.status == 'TELAT'"
                                    class="fs-12"
                                  >
                                    Alasan:
                                    <b>{{
                                      data.presensi?.presensi_terlambat?.ket ??
                                      "-"
                                    }}</b>
                                  </span>
                                </span>
                                <span
                                  v-else-if="data.status === null"
                                  class="badge bg-secondary-subtle text-muted"
                                >
                                  BELUM ABSEN
                                </span>
                                <span
                                  v-else-if="
                                    data.status === IZIN &&
                                    data.izin_detail !== null
                                  "
                                  class="badge bg-warning-subtle text-muted"
                                >
                                  {{ data.izin_detail.izin.izin }}
                                </span>
                                <span
                                  v-else-if="data.status === TUKAR_OFF"
                                  class="badge bg-info-subtle text-danger"
                                >
                                  Tukar Off
                                </span>
                                <span
                                  v-else-if="data.status === SPPD"
                                  class="badge bg-info-subtle text-muted"
                                >
                                  SPPD
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </BCardBody>
                    </BCard>
                  </BCol>
                </BRow>
                <h4 v-else-if="progress" class="text-center">Tunggu Dulu...</h4>
                <h4 v-else-if="listPresensi.length < 1" class="text-center">
                  Tidak ada Data
                </h4>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
    </div>
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
  progressMethods,
  progressState,
  spinnerMethods,
  toastMethods,
} from "@/state/helpers";
import { onProgressService } from "@/services/OnProgressService";
import { mUnitService } from "@/services/MUnitService";
import queryString from "query-string";
import { webUrl } from "@/config/http";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

export default {
  components: {
    Layout,
    PageHeader,
  },
  data() {
    return {
      listPresensi: [],
      listTemp: [],
      units: [],
      IZIN: 4,
      TUKAR_OFF: 6,
      SPPD: 7,
      progress: false,
    };
  },
  computed: {
    ...progressState,
  },
  watch: {
    reload() {
      this.fetchData();
    },
  },
  created() {
    Promise.all([this.getUnit(), this.fetchData()]);
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...progressMethods,
    async fetchData() {
      this.progress = true;

      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await onProgressService.all(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.progress = false;
        this.listPresensi = [];

        return;
      }

      this.listPresensi = resp.data;
      this.progress = false;
    },
    onRefresh() {
      this.resetFilter();
    },
    onFilterSearchFn(event) {
      let val = event.target.value;
      if (val.length > 3) {
        this.onFilterSearch(val);
      }
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    onFilterStatusFn(val) {
      this.onFilterStatus(val);
    },
    showBukti(params) {
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
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
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
  },
};
</script>
