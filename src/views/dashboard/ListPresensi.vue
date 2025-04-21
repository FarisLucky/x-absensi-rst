<template>
  <BCard no-body class="mb-2">
    <BCardBody>
      <BRow class="job-list-row g-2" id="candidate-list">
        <BCol lg="12">
          <div class="d-flex justify-content-between mb-1 align-items-center">
            <span class="fs-14 font-weight">Terakhir Presensi Masuk</span>
            <router-link
              :to="{ name: 'ProgressPresensi' }"
              class="pb-1 btn btn-sm btn-light"
              opacity="75"
            >
              Tampilkan Lebih Banyak
              <i class="ri-arrow-right-fill"></i>
            </router-link>
          </div>
        </BCol>
        <template v-if="listPresensi.length > 0">
          <BCol xxl="3" md="4" v-for="(data, idx) in listPresensi" :key="idx">
            <BCard no-body class="mb-1">
              <BCardBody class="p-2">
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <div
                      v-if="data?.m_karyawan?.photo !== null"
                      class="rounded avatar-md border border-secondary"
                    >
                      <BLink @click.prevent="showBukti('.img-' + data.nip)">
                        <img
                          :src="getProfil(data.m_karyawan?.nip)"
                          alt="karyawan-img"
                          class="img-fluid"
                        />
                      </BLink>
                      <div
                        class="images d-none"
                        :class="'img-' + data.nip"
                        v-viewer="{ movable: false }"
                      >
                        <img
                          v-for="src in [data.m_karyawan.photo_url_cast]"
                          :key="src"
                          :src="src"
                        />
                      </div>
                    </div>
                    <div v-else class="avatar-md">
                      <img
                        v-if="data?.m_karyawan?.sex === 'L'"
                        src="@/assets/images/male.png"
                        class="member-img img-fluid d-block rounded"
                      />
                      <img
                        v-else
                        src="@/assets/images/female.png"
                        class="member-img img-fluid d-block rounded"
                      />
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <div class="overflow overflow-hidden">
                      <router-link
                        :to="{
                          name: 'ProfileKaryawan',
                          params: {
                            nip: data?.nip,
                          },
                        }"
                      >
                        <div class="text-muted mb-1" style="height: 20px">
                          <h5 class="fs-16 mb-1 over-text">
                            {{ data?.m_karyawan.nama }}
                          </h5>
                        </div>
                      </router-link>
                      <div class="badge badge-gradient-info over-text">
                        {{ data?.m_karyawan?.unit }}
                      </div>
                      <div class="text-muted mt-1">
                        <i
                          class="ri-time-line text-primary me-1 align-bottom"
                        ></i>
                        <span>Masuk : </span>
                        {{
                          `${data?.jadwal?.tanggal_cast} ${data?.jadwal?.jam_masuk}`
                        }}
                      </div>
                      <div class="text-muted">
                        <div class="mt-1">
                          <i
                            class="ri-map-pin-2-line text-primary me-1 align-bottom"
                          ></i>
                          <span>Absen : </span>
                          {{
                            `${data?.jadwal?.tanggal_cast} ${data?.jadwal?.masuk}`
                          }}
                          <span
                            class="badge"
                            :class="{
                              'badge-gradient-primary':
                                data?.jadwal?.status_absen == 'TEPAT',
                              'badge-gradient-danger':
                                data?.jadwal?.status_absen == 'TELAT',
                            }"
                            >{{ data?.jadwal?.status_absen }}</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </BCardBody>
            </BCard>
          </BCol>
        </template>
        <BCol v-else cols="12">
          <BCard>
            <h4 class="py-3 border rounded text-center">Belum Ada Presensi</h4>
          </BCard>
        </BCol>
      </BRow>
    </BCardBody>
  </BCard>
</template>
<script>
import { webUrl } from "@/config/http";
import { dashboardService } from "@/services/DashboardService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

export default {
  data() {
    return {
      listPresensi: [],
      pages: null,
      pageNumber: null,
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
    ...toastMethods,
    ...spinnerMethods,
    async fetchData() {
      this.show();
      const [err, resp] = await dashboardService.progress();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.listPresensi = resp.data;
      this.hide();
    },
    getProfil(nip) {
      return `${webUrl}/profil/${nip}`;
    },
    showBukti(params) {
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
  },
};
</script>
<style scoped>
.over-text {
  white-space: nowrap;
  width: 160px;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
