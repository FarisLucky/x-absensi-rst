<template lang="">
  <BModal
    v-model="modal"
    id="tracking"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    :title="Tracking"
  >
    <l-map
      :zoom="zoom"
      :center="[latCircle, lngCircle]"
      :fade-animation="true"
      :marker-zoom-animation="true"
      ref="mapLeaflet"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      ></l-tile-layer>
      <l-control-layers />
      <l-marker :lat-lng="[lat, lng]">
        <l-tooltip> lol </l-tooltip>
      </l-marker>
      <l-circle
        :lat-lng="[latCircle, lngCircle]"
        :radius="radius"
        :fill="true"
        color="#F06A4E"
      >
      </l-circle>
    </l-map>
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
  LCircle,
} from "@vue-leaflet/vue-leaflet";
import { LatLng } from "leaflet";
import { Geolocation } from "@capacitor/geolocation";
export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LControlLayers,
    LTooltip,
    LCircle,
  },
  data() {
    return {
      zoom: 17,
      iconWidth: 25,
      iconHeight: 40,
      lat: "",
      lng: "",
      latCircle: 0,
      lngCircle: 0,
      radius: 30,
      modal: false,
    };
  },
  methods: {
    showModal() {
      this.onPresenceLocation();
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
    },
    onPresenceLocation() {
      this.$refs.mapLeaflet.leafletObject.flyTo(
        new LatLng(this.latCircle, this.lngCircle),
        this.zoom
      );
    },
  },
};
</script>
