<template>
  <div>
    <BCard no-body class="p-0">
      <BCardBody>
        <BTabs nav-class="nav-border-top nav-border-top-primary mb-3">
          <BTab
            v-for="(tab, idx) in list"
            :key="idx"
            :active="$route.params.jenis === tab.route"
            lazy
            @click.prevent="onChangeTab(tab.route)"
          >
            <template #title>
              <i class="align-middle me-1" :class="tab.icon"></i>
              {{ tab.title }}
            </template>
            <KeepAlive>
              <component :is="tab.component" />
            </KeepAlive>
          </BTab>
        </BTabs>
      </BCardBody>
    </BCard>
  </div>
</template>
<script>
import { defineAsyncComponent } from "vue";

export default {
  components: {
    ProfilStatistik: defineAsyncComponent(() =>
      import("@/views/profil/ProfilStatistik")
    ),
    ProfilDiri: defineAsyncComponent(() => import("@/views/profil/ProfilDiri")),
    ProfilIzin: defineAsyncComponent(() => import("@/views/profil/ProfilIzin")),
    ProfilPresensi: defineAsyncComponent(() =>
      import("@/views/profil/ProfilPresensi")
    ),
  },
  data() {
    return {
      list: [
        {
          title: "Data Diri",
          component: "ProfilDiri",
          route: "datadiri",
          icon: "ri-shield-user-line",
        },
        // {
        //   title: "Tren Kehadiran Bulanan",
        //   component: "ProfilStatistik",
        //   route: "statistik",
        //   icon: "ri-file-chart-line",
        // },
        {
          title: "Kehadiran",
          component: "ProfilPresensi",
          route: "presensi",
          icon: "ri-user-location-line",
        },
        {
          title: "Izin",
          component: "ProfilIzin",
          route: "izin",
          icon: "ri-plane-line",
        },
        {
          title: "Dokumen",
          component: "ProfilDokumen",
          route: "ProfilDokumen",
          icon: "ri-file-fill",
        },
      ],
      active: "ProfilPribadi",
    };
  },
  computed: {
    currentTab() {
      let current = this.list.find(
        (item) => item.route === this.$route.params.jenis
      );

      return current ? current.component : null;
    },
  },
  methods: {
    onChangeTab(params) {
      this.$router.push({
        name: "ProfileKaryawan",
        params: { nip: this.$route.params.nip, jenis: params },
      });
    },
  },
};
</script>
