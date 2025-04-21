<template>
  <Layout>
    <PageHeader title="Rekap Presensi" pageTitle="Rekap" />
    <BCard no-body>
      <BCardBody>
        <p class="text-muted">Laporan presensi berdasarkan data sistem.</p>
        <BTabs
          navClass="animation-nav gap-2 mb-2 border rounded"
          pills
          justified
        >
          <BTab
            v-for="(tab, idx) in list"
            :title="tab.title"
            :active="$route.name === tab.route"
            :key="idx"
          >
            <component :is="tab.component" />
          </BTab>
        </BTabs>
      </BCardBody>
    </BCard>
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import { defineAsyncComponent } from "vue";
// import { SUPER_ADMIN, KASUB, KABID, DIREKTUR } from "@/helpers/utils";

export default {
  components: {
    Layout,
    PageHeader,
    Rekap: defineAsyncComponent(() => import("./Rekap.vue")),
    RekapKaryawan: defineAsyncComponent(() => import("./RekapKehadiran.vue")),
    RekapBulanan: defineAsyncComponent(() => import("./RekapBulanan.vue")),
    RekapLembur: defineAsyncComponent(() => import("./RekapLembur.vue")),
    RekapTerlambat: defineAsyncComponent(() => import("./RekapTerlambat.vue")),
    RekapPerizinan: defineAsyncComponent(() => import("./RekapPerizinan.vue")),
    RekapJadwal: defineAsyncComponent(() => import("./RekapJadwal.vue")),
  },
  data() {
    return {
      list: [
        {
          title: "Harian",
          component: "Rekap",
          route: "RekapPresensi",
          //   for: this.isKaBid || this.isKaSub || this.isDir || this.isSuperAdmin,
        },
        {
          title: "Bulanan",
          component: "RekapBulanan",
          route: "RekapBulanan",
          //   for: true,
        },
        {
          title: "Kehadiran",
          component: "RekapKaryawan",
          route: "RekapKaryawan",
          //   for: true,
        },
        // {
        //   title: "Lembur",
        //   component: "RekapLembur",
        //   route: "RekapLembur",
        //   //   for: true,
        // },
        // {
        //   title: "Terlambat",
        //   component: "RekapTerlambat",
        //   route: "RekapTerlambat",
        //   //   for: true,
        // },
        // {
        //   title: "Izin",
        //   component: "RekapPerizinan",
        //   route: "RekapPerizinan",
        //   //   for: true,
        // },
        // {
        //   title: "Jadwal",
        //   component: "RekapJadwal",
        //   route: "RekapJadwal",
        //   //   for: true,
        // },
      ],
    };
  },
  //   computed: {
  //     isSuperAdmin() {
  //       return this.$store.state.auth.data.role === SUPER_ADMIN;
  //     },
  //     isKaSub() {
  //       return this.$store.state.auth.data.role === KASUB;
  //     },
  //     isKaBid() {
  //       return this.$store.state.auth.data.role === KABID;
  //     },
  //     isDir() {
  //       return this.$store.state.auth.data.role === DIREKTUR;
  //     },
  //   },
};
</script>
