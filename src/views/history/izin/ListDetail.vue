<template>
  <BRow>
    <BCol md="12">
      <BCard no-body class="mb-2">
        <BCardBody>
          <div class="d-flex justify-content-between">
            <h5 class="fs-18">Detail Izin</h5>
          </div>
          <div class="d-flex ms-2">
            <div class="flex-shrink-0">
              <div
                v-if="
                  izin?.pemohon?.photo !== null && izin?.pemohon?.photo !== ''
                "
                class="rounded-3 avatar-md border border-secondary"
              >
                <BLink @click.prevent="showBukti('.img-' + izin?.nip)">
                  <img
                    :src="izin?.pemohon?.photo_url_cast"
                    alt="karyawan-img"
                    class="img-fluid"
                  />
                </BLink>
                <div
                  class="images d-none"
                  :class="'img-' + izin?.nip"
                  v-viewer="{ movable: false }"
                >
                  <img :src="izin?.pemohon?.photo_url_cast" />
                </div>
              </div>
              <div v-else class="avatar-md rounded">
                <img
                  src="@/assets/images/profil.jpg"
                  class="member-img img-fluid d-block rounded"
                />
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div
                class="overflow-hidden mt-1 text-muted mb-1"
                style="height: 20px"
              >
                <h5 class="fs-16 mb-1 overflow-hidden">
                  {{ izin?.nama }}
                </h5>
              </div>
              <div class="badge badge-gradient-info">
                {{ izin?.unit }}
              </div>
            </div>
          </div>
        </BCardBody>
      </BCard>
    </BCol>
    <BCol md="7">
      <BCard no-body>
        <BCardBody>
          <div class="d-flex justify-content-between mb-2">
            <h5 class="mb-0">Pengajuan Izin</h5>
            <span class="bg-soft-secondary text-secondary p-2 rounded">{{
              izin.acc_status_desc
            }}</span>
          </div>
          <BRow class="g-2">
            <BCol cols="6" lg="4">
              <label>Pengajuan</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.created_at_cast"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Kode</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.kode_izin"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Izin</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.izin"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Tgl Mulai</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.mulai_day_cast}, ${izin?.mulai_cast}`"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Tgl Akhir</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.akhir_day_cast}, ${izin?.akhir_cast}`"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Tgl Masuk</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.masuk_day_cast}, ${izin?.masuk_cast}`"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Periode</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.periode} hari`"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Cuti Diambil</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.cuti_diambil ?? '-'} hari`"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>Sisa Cuti</label>
              <input
                type="text"
                class="form-control"
                :value="`${izin?.sisa ?? '-'} hari`"
                disabled
              />
            </BCol>
            <BCol cols="12">
              <label>Keterangan</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.ket ?? '-'"
                disabled
              />
              <hr />
            </BCol>
            <BCol cols="6" lg="4">
              <label>ACC Nip</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.acc_nip"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>ACC Nama</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.acc_nama"
                disabled
              />
            </BCol>
            <BCol cols="6" lg="4">
              <label>ACC Pada</label>
              <input
                type="text"
                class="form-control"
                :value="izin?.acc_at_cast"
                disabled
              />
            </BCol>
          </BRow>
        </BCardBody>
      </BCard>
    </BCol>
    <BCol>
      <BCard no-body>
        <BCardBody>
          <h5 class="fs-14">Riwayat 5 Izin Terakhir</h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Izin</th>
                  <th>Mulai</th>
                  <th>Akhir</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(izinVal, idx) in lastIzins" :key="idx">
                  <td>{{ ++idx }}</td>
                  <td>{{ izinVal.izin }}</td>
                  <td>{{ izinVal.mulai_cast }}</td>
                  <td>{{ izinVal.akhir_cast }}</td>
                  <td>
                    <div
                      class="p-1 rounded text-center fs-14"
                      style="cursor: pointer"
                    >
                      <span
                        v-if="izinVal.acc_status == 1"
                        class="badge badge-gradient-dark"
                      >
                        PROGRESS
                      </span>
                      <span
                        v-else-if="izinVal.acc_status == 2"
                        class="badge badge-gradient-success"
                      >
                        Selesai
                      </span>
                      <span
                        v-else-if="izinVal.acc_status == 3"
                        class="badge badge-gradient-danger"
                      >
                        DITOLAK
                      </span>
                      <span
                        v-else-if="izinVal.acc_status == 4"
                        class="badge badge-gradient-danger"
                      >
                        BATAL
                      </span>
                    </div>
                  </td>
                  <td>
                    <button
                      v-b-tooltip="'Lihat Detail'"
                      class="btn btn-sm btn-soft-primary me-1"
                      @click.prevent="showLastIzin(izinVal)"
                    >
                      <i class="ri-play-line"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </BCardBody>
      </BCard>
    </BCol>
  </BRow>
</template>
<script>
import animationData4 from "@/components/widgets/pithnlch.json";
import {
  SUPER_ADMIN,
  TELAT,
  TEPAT,
  IZIN_CUTI,
  IZIN_KRS,
} from "@/helpers/utils";
import { izinService } from "@/services/IzinService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

const initForm = () => ({
  id: "",
});

export default {
  data() {
    return {
      animationData4,
      form: initForm(),
      izin: {},
      lastIzins: [],
      TELAT,
      IZIN_CUTI,
      IZIN_KRS,
      TEPAT,
    };
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  async created() {
    await this.showIzin(this.$route.params.id);
    this.getLastIzin(this.izin.nip);
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async showIzin(id) {
      this.show();
      const [err, resp] = await izinService.show(id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.izin = resp.data;
      this.hide();
    },
    async getLastIzin(nip) {
      this.show();
      const [err, resp] = await izinService.getLastIzin(nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.lastIzins = resp.data;
      this.hide();
    },
    showLastIzin(params) {
      this.showIzin(params.id);
      this.$route.params.id = params.id;
    },
    showBukti(params) {
      // const viewer = params.$viewer;
      const viewer = this.$el.querySelector(params).$viewer;
      viewer.show();
    },
  },
};
</script>
