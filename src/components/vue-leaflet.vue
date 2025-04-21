<template>
  <div class="bg-layer">
    <div style="height: 500px; width: 100%" id="mapTemplate"></div>
  </div>
</template>
<script>
import "leaflet/dist/leaflet.css";
import * as L from "leaflet";

export default {
  data() {
    return {
      lat: -7.7558137,
      lng: 113.4395386,
      latCircle: -7.7558137,
      lngCircle: 113.4395386,
      radius: 30,
      map: null,
    };
  },
  mounted() {
    this.map = L.map("mapTemplate", {
      center: new L.LatLng(this.latCircle, this.lngCircle),
      zoom: 19,
    });
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution:
        '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(this.map);
    L.circle([this.latCircle, this.lngCircle], {
      color: "red",
      fillColor: "#f03",
      fillOpacity: 0.5,
      radius: 30,
    }).addTo(this.map);
  },
  methods: {
    getLocation() {
      alert("test");
      const success = (position) => {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        alert(latitude);
        alert(longitude);
        this.lat = latitude;
        this.lng = longitude;

        L.marker([this.lat, this.lng]).addTo(this.$refs.maps.value);
        this.$refs.maps.value.flyTo(new L.LatLng(this.lat, this.lng), 19);

        // Do something with the position
      };

      const error = (err) => {
        console.log(err);
      };

      // This will open permission popup
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error, {
          enabledHighAccuracy: true,
          timeout: 5000,
        });
      } else {
        alert("GEOLOCATION TIDAK SUPPORT");
      }
    },
  },
};
</script>
