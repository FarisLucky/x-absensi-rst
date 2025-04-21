<template>
  <BModal
    v-model="modal"
    id="lihatJadwal"
    modal-class="zoomIn"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    title="Lihat Lokasi"
    size="lg"
  >
    <div>
      <BRow class="g-2">
        <BCol cols="4">
          <label for="lat">Lokasi Masuk</label>
          <input type="text" class="form-control" v-model="form.lokasi" />
        </BCol>
        <BCol cols="4">
          <label for="lat">Latitude</label>
          <input type="text" class="form-control" v-model="form.latitude" />
        </BCol>
        <BCol cols="4">
          <label for="lat">Longitude</label>
          <input type="text" class="form-control" v-model="form.longitude" />
        </BCol>
      </BRow>
      <div class="bg-layer text-center mt-3">
        <div
          v-if="showMap"
          style="height: 70vh; width: 100%; margin-bottom: 0.7rem"
        >
          <l-map
            :zoom="zoom"
            :center="[form.latitude, form.longitude]"
            :fade-animation="true"
            :marker-zoom-animation="true"
            ref="mapLeaflet"
          >
            <l-tile-layer
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            ></l-tile-layer>
            <l-control-layers />
            <l-marker :lat-lng="[form.latitude, form.longitude]">
              <l-tooltip> koordinat absen </l-tooltip>
            </l-marker>
            <l-circle
              :lat-lng="[form.latitude, form.longitude]"
              :radius="form.radius"
              :fill="true"
              color="#F06A4E"
            >
            </l-circle>
            <l-circle
              v-for="(lok, idx) in lokasi"
              :key="idx"
              :name="lok?.nama"
              :lat-lng="[lok?.latitude, lok?.longitude]"
              :radius="lok?.radius"
              :fill="true"
              color="#F06A4E"
            >
              <l-popup>{{ lok?.nama }} - Radius: {{ lok?.radius }} m</l-popup>
            </l-circle>
          </l-map>
        </div>
      </div>
    </div>
  </BModal>
</template>
<script>
import "leaflet/dist/leaflet.css";
import {
  LMap,
  LTileLayer,
  LMarker,
  LControlLayers,
  LTooltip,
  LPopup,
  LCircle,
} from "@vue-leaflet/vue-leaflet";
import { toastMethods } from "@/state/helpers";
import { mLokasiService } from "@/services/MLokasiService";
// import { jadwalService } from "@/services/JadwalService";

export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LControlLayers,
    LTooltip,
    LPopup,
    LCircle,
  },
  data() {
    return {
      modal: false,
      zoom: 16,
      marker: null,
      showMap: false,
      form: {
        lokasi: "",
        latitude: "-7.7149803389768",
        longitude: "113.58001828194",
      },
      lokasi: [],
    };
  },
  methods: {
    ...toastMethods,
    async showModal() {
      this.modal = true;
      await this.getLokasi();
      this.showMap = true;
    },
    hideModal() {
      this.modal = false;
      this.showMap = false;
    },
    reset() {
      this.form = {
        lokasi: "",
        latitude: "-7.7149803389768",
        longitude: "113.58001828194",
      };
      this.lokasi = [];
    },
    toggleMap() {
      this.showMap = !this.showMap;
    },
    async getLokasi() {
      let aktif = 1;
      const [err, resp] = await mLokasiService.data(aktif);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.lokasi = resp.data;
    },
  },
};
</script>
