<template>
  <div id="app1">
    <transition name="slide-bottom" mode="out-in">
      <v-spinner></v-spinner>
    </transition>
    <vue3-confirm-dialog></vue3-confirm-dialog>
    <PWAHandler v-if="platform !== 'android'" />
    <router-view></router-view>
    <RingBottomNavigation
      v-if="mobileScreen && auth"
      border-color="#064e3b"
      title-color="#064e3b"
      badge-color="#f87171"
      icon-color="#064e3b"
      :options="options"
      v-model="selected"
    />
    <SonnerToaster />
  </div>
</template>

<script>
import { RingBottomNavigation } from "bottom-navigation-vue";
import "bottom-navigation-vue/dist/style.css";
import VSpinner from "@/components/v-spinner";
import { getCurrentInstance } from "vue";
import { TextZoom } from "@capacitor/text-zoom";
import { Device } from "@capacitor/device";
import SonnerToaster from "@/components/SonnerToaster.vue";
import PWAHandler from "@/components/PWAHandler.vue";

export default {
  name: "App",
  components: {
    RingBottomNavigation,
    VSpinner,
    SonnerToaster,
    PWAHandler,
  },
  data: () => ({
    selected: 1,
    mobileScreen: false,
    options: [
      {
        id: 1,
        icon: "ri-home-3-line",
        title: "Home",
        path: { name: "Dashboard" },
      },
      {
        id: 2,
        icon: "ri-user-location-fill",
        title: "Presensi",
        path: { name: "PresensiMain" },
      },
      {
        id: 3,
        icon: "ri-calendar-check-fill",
        title: "Jadwal",
        path: { name: "JadwalList" },
      },
      {
        id: 4,
        icon: "ri-history-fill",
        title: "History",
        path: { name: "HistoryPresensi" },
      },
    ],
    listenersStarted: false,
    idToken: "",
    app: getCurrentInstance(),
    platform: "",
  }),
  computed: {
    auth() {
      return this.$store.state?.auth?.data?.role !== undefined;
    },
  },
  async created() {
    var windowSize = document.documentElement.clientWidth;
    if (windowSize < 767) {
      this.mobileScreen = true;
    } else {
      this.mobileScreen = false;
    }
    this.platform = await Device.getInfo().platform;

    if (["android", "ios"].includes(this.platform)) {
      this.setTextZoom(1.0);
    }
  },
  mounted() {
    document.addEventListener("contextmenu", (e) => {
      e.preventDefault();
      return false;
    });
    // Prevent double-tap zoom
    let lastTouchEnd = 0;
    document.addEventListener(
      "touchend",
      (event) => {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
          event.preventDefault();
        }
        lastTouchEnd = now;
      },
      false
    );
  },
  methods: {
    async setTextZoom(zoomLevel) {
      try {
        await TextZoom.set({ value: zoomLevel });
        console.log("Text zoom set to:", zoomLevel);
      } catch (error) {
        console.error("Error setting text zoom:", error);
      }
    },
  },
};
</script>
<style>
.rg-btn-container {
  background: #fafafa;
}
</style>
