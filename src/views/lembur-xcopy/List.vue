<template>
  <BRow>
    <BCol md="12">
      <div
        v-if="presensiPulang !== null"
        class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show mb-1"
        role="alert"
      >
        <i class="ri-error-warning-line label-icon"></i
        ><span>
          Anda masih memiliki presensi aktif. Check
          <BLink
            variant="text-warning"
            @click.prevent="() => window.location.reload()"
            >klik disini</BLink
          ></span
        >
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss=" alert"
          aria-label="Close"
        ></button>
      </div>
      <div
        v-else-if="!inRadius"
        class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show mb-1"
        role="alert"
      >
        <i class="ri-error-warning-line label-icon"></i
        ><span
          >Diluar Jangkauan Radius, Cek Lokasi saya
          <BLink variant="text-warning ms-1" @click.prevent="onMyLocation"
            >disini</BLink
          ></span
        >
      </div>
    </BCol>
    <BCol xl="12">
      <div class="mb-1">
        <h5 class="fs-14 pb-1 mb-2 border-bottom d-inline-block">
          List Lembur
        </h5>
        <BForm>
          <BRow class="g-2">
            <BCol lg="2">
              <v-select
                v-model="filter.year"
                :options="years"
                placeholder="Pilih Tahun"
              ></v-select>
            </BCol>
            <BCol v-if="filter.year > 0" lg="2">
              <v-select
                v-model="filter.month"
                :options="months"
                :reduce="(l) => l.id"
                label="name"
                placeholder="Pilih Bulan"
                @option:selected="fetchData"
              ></v-select>
            </BCol>
            <BCol>
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
        </BForm>
      </div>
      <div class="mb-1 table-responsive">
        <vue-good-table
          mode="local"
          :columns="columns"
          :rows="rows"
          :pagination-options="{
            enabled: true,
          }"
          :isLoading="isLoading"
          :line-numbers="true"
          :sort-options="{
            enabled: false,
          }"
          theme="polar-bear"
          styleClass="vgt-table striped sticky"
        >
          <template #table-row="props">
            <span v-if="props.column.field == 'acc_status'">
              <strong v-if="props.row.acc_status == 1" class="text-success">
                Diterima
                <i class="ri-checkbox-circle-fill"></i>
              </strong>
            </span>
            <span
              v-if="
                props.row?.absen == 'GPS' &&
                props.column.field == 'masuk' &&
                props.row.m_lembur?.id_lokasi !== null
              "
            >
              <div v-if="props.row.masuk == null && props.row.acc2_at !== null">
                <BLink
                  v-if="!inRadius"
                  variant="primary"
                  @click.prevent="
                    preAbsen({
                      id_lokasi: props.row.id_jenis,
                    })
                  "
                >
                  <i class="ri-map-pin-line"></i>
                  Lokasi Saya
                </BLink>
                <BLink
                  v-else-if="presensiPulang == null && presensiMasuk != null"
                  variant="primary"
                  @click.prevent="onPulang"
                >
                  <i class="ri-map-pin-line"></i>
                  Checkout Presensi
                </BLink>
                <BButton
                  v-else-if="inRadius"
                  variant="primary"
                  @click.prevent="
                    onAbsen({
                      id: props.row.id,
                      jenis: 'MASUK',
                      posisi: {
                        lat: lat,
                        lng: lng,
                      },
                    })
                  "
                >
                  <i class="ri-map-pin-line"></i>
                  Masuk
                </BButton>
              </div>
              <div
                v-else-if="
                  props.row.masuk !== null &&
                  (props.row.keluar === '0000-00-00 00:00:00' ||
                    props.row.keluar === null)
                "
              >
                <BLink
                  v-if="!inRadius"
                  variant="primary"
                  @click.prevent="onMyLocation"
                >
                  <i class="ri-map-pin-line"></i>
                  Lokasi Saya
                </BLink>
                <BButton
                  v-else-if="inRadius"
                  variant="danger"
                  @click.prevent="
                    onAbsen({
                      id: props.row.id,
                      jenis: 'KELUAR',
                      posisi: {
                        lat: lat,
                        lng: lng,
                      },
                    })
                  "
                >
                  <i class="ri-map-pin-line"></i>
                  Keluar
                </BButton>
              </div>
              <div v-else class="bg-success-subtle p-1 rounded d-inline-block">
                <strong>Selesai</strong>
              </div>
            </span>
            <div
              v-else-if="
                props.row?.absen == 'FOTO' &&
                props.row.masuk !== null &&
                props.column.field == 'masuk'
              "
              class="bg-success-subtle p-1 rounded d-inline-block"
            >
              <button
                @click.prevent="showBukti('.img' + props.row.id)"
                class="btn btn-sm btn-soft-success waves-effect waves-light"
              >
                <i class="ri-image-2-fill"></i>
                Lihat
              </button>
              <div
                class="images d-none"
                :class="'img-' + props.row?.id"
                v-viewer="{ movable: false }"
              >
                <img
                  v-for="src in [props.row.bukti_url_cast]"
                  :key="src"
                  :src="src"
                />
              </div>
            </div>
            <span
              v-else-if="
                props.column.field == 'masuk' &&
                props.row?.masuk == null &&
                props.row.absen == 'FOTO'
              "
            >
              <BButton
                variant="primary"
                @click.prevent="openCamera({ id: props.row.id })"
              >
                <i class="ri-camera-fill"></i>
                Masuk
              </BButton>
            </span>
            <div
              v-else-if="
                props.column.field == 'masuk' && props.row.absen == 'BACKDATE'
              "
              class="bg-success-subtle p-1 rounded d-inline-block"
            >
              <strong>BACKDATE</strong>
            </div>
          </template>
        </vue-good-table>
      </div>
    </BCol>
    <LemburMasukFoto ref="lemburMasukFotoRef" @fetch="fetchData" />
  </BRow>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import { months, getYears } from "@/helpers/utils";
import { lemburService } from "@/services/LemburService";
import { Geolocation } from "@capacitor/geolocation";
import { presensiService } from "@/services/PresensiService";
import { mLokasiService } from "@/services/MLokasiService";
import LemburMasukFoto from "./LemburMasukFoto";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";

const initFilter = () => {
  const currentTime = new Date();

  return {
    bawahan: "unit",
    month: currentTime.getMonth() + 1,
    year: currentTime.getFullYear(),
  };
};

export default {
  components: {
    VueGoodTable,
    LemburMasukFoto,
  },
  data() {
    const user = this.$store.state?.auth?.data;

    return {
      filter: initFilter(),
      columns: [
        {
          label: "Unit",
          field: "m_unit.nama",
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Tanggal",
          field: "tanggal_cast",
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
          label: "Ket",
          field: "ket",
        },
        {
          label: "Status",
          field: "acc_status",
        },
        {
          label: "Absen",
          field: "masuk",
        },
      ],
      rows: [],
      isLoading: false,
      user,
      months,
      years: [],
      lat: "",
      lng: "",
      inRadius: false,
      presensiPulang: null,
      presensiMasuk: null,
      latCircle: 0,
      lngCircle: 0,
      id_lokasi: 0,
      radius: 0,
      device: "web",
      last_ip: "",
    };
  },
  created() {
    this.fetchData();
    this.years = getYears();
    this.checkPresensiPulang();
    this.checkPresensiMasuk();
    this.getLocation();
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
      this.isLoading = true;

      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });
      const [err, resp] = await lemburService.selesai(query);
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
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
      this.getLocation();
    },
    async onMyLocation() {
      try {
        const pos = await Geolocation.getCurrentPosition({
          enableHighAccuracy: true,
          maximumAge: 5000,
        });

        this.lat = pos.coords.latitude;
        this.lng = pos.coords.longitude;
        this.checkRadius();
      } catch (error) {
        if (error.code == 1) {
          this.gpsOn = false;
          this.toastError({
            title: "Gagal",
            msg: "Silahkan terima akses lokasi dan muat Ulang",
          });
        }
      }
    },
    async checkRadius(idLokasi = null) {
      let form = {
        latitude: this.lat,
        longitude: this.lng,
        id_lokasi: idLokasi,
      };
      const [err] = await presensiService.radiusValidation(form);
      if (err) {
        this.inRadius = false;
        return;
      }
      this.inRadius = true;
    },
    async checkPresensiPulang() {
      this.show();
      const [err, resp] = await presensiService.checkPresensiPulang();
      if (err) {
        this.hide();
        return;
      }
      this.presensiPulang = resp.data;
      this.hide();
    },
    async checkPresensiMasuk() {
      this.show();
      const [err, resp] = await presensiService.checkPresensiMasuk();
      if (err) {
        this.hide();
        this.presensiPulang = null;
        return;
      }
      this.presensiMasuk = resp.data;
      this.hide();
    },
    async preAbsen(params) {
      await this.getLocationMasuk(params.id_lokasi);
      await this.onMyLocation();
      await this.checkRadius(params.id_lokasi);
    },
    async onAbsen(params) {
      this.show();
      const [err] = await lemburService.absen(params);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "OK",
      });
      this.fetchData();
      this.hide();
    },
    async onPulang() {
      const [err] = await presensiService.pulang({
        id_lokasi: this.id_lokasi,
        latitude: this.lat,
        longitude: this.lng,
        last_ip: this.last_ip,
        device: this.device,
      });
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "Presensi Berhasil",
      });
      this.$router.push({ name: "Jadwalku" });
    },
    async getLocation() {
      let aktif = 1;
      const [err, resp] = await mLokasiService.data(aktif);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }

      this.id_lokasi = resp.data[0].id;
      this.latCircle = resp.data[0].latitude;
      this.lngCircle = resp.data[0].longitude;
      this.radius = resp.data[0].radius;
    },
    async getLocationMasuk(id) {
      const [err, resp] = await mLokasiService.show(id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }

      this.id_lokasi = resp.data.id;
      this.latCircle = resp.data.latitude;
      this.lngCircle = resp.data.longitude;
      this.radius = resp.data.radius;
    },
    async openCamera(params) {
      this.$refs.lemburMasukFotoRef.id = params.id;
      this.$refs.lemburMasukFotoRef.showModal();
    },
    showBukti(params) {
      // const viewer = params.$viewer;
      console.log(params);
      const viewer = this.$el.querySelector(params).$viewer;
      console.log(viewer);
      viewer.show();
    },
  },
};
</script>
