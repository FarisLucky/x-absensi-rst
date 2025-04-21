<template>
  <Layout style="height: 250vh">
    <PageHeader :title="title" pageTitle="Pages" />
    <BRow class="g-2 mb-1">
      <BCol lg="7">
        <BCard style="height: 87%">
          <div class="mb-3">
            <h4 class="fw-semibold fs-16 pb-1 d-inline-block">
              Halo ! {{ $store.state.auth.data.nama }} ({{
                $store.state.auth.data.nip
              }})
            </h4>
          </div>
          <TableMenu />
        </BCard>
      </BCol>
      <BCol lg="5">
        <BRow class="g-2 align-items-center">
          <BCol cols="12" md="9">
            <TableJadwalHarian :jadwal="vJadwalku[0] ?? []" jenis="DINAS" />
            <TableJadwalHarian
              v-if="lembur !== null"
              :jadwal="lembur"
              jenis="LEMBUR"
            />
          </BCol>
        </BRow>
      </BCol>
    </BRow>
    <GrafikKehadiran v-if="isSuperAdmin" />
    <BRow v-if="isSuperAdmin" class="mb-1 mt-2 g-2">
      <BCol md="6">
        <GrafikGender :male-count="10" :female-count="20" />
      </BCol>
      <BCol md="6">
        <TableJarangHadir />
      </BCol>
    </BRow>
    <BRow v-if="isSuperAdmin" class="mb-1 g-2">
      <BCol md="5">
        <TablePresensiHarian />
      </BCol>
      <BCol md="7">
        <TableIzinHarian />
      </BCol>
    </BRow>
    <list-presensi v-if="isSuperAdmin" />
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import quote from "quote-indo";
import { dashboardService } from "@/services/DashboardService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { SUPER_ADMIN } from "@/helpers/utils";
import { defineAsyncComponent } from "vue";
import { http } from "@/config";
import { Device } from "@capacitor/device";

export default {
  components: {
    Layout,
    PageHeader,
    ListPresensi: defineAsyncComponent(() => import("./ListPresensi")),
    TablePresensiHarian: defineAsyncComponent(() =>
      import("./TablePresensiHarian")
    ),
    TableIzinHarian: defineAsyncComponent(() => import("./TableIzinHarian")),
    TableJadwalHarian: defineAsyncComponent(() =>
      import("./TableJadwalHarian")
    ),
    // TableHadirHarian: defineAsyncComponent(() => import("./TableHadirHarian")),
    TableMenu: defineAsyncComponent(() => import("./TableMenu")),
    GrafikGender: defineAsyncComponent(() => import("./GrafikGender")),
    GrafikKehadiran: defineAsyncComponent(() => import("./GrafikKehadiran")),
    TableJarangHadir: defineAsyncComponent(() => import("./TableJarangHadir")),
  },
  data() {
    return {
      myQuote: "",
      list: [
        {
          title: "Masuk Bulan Ini",
          ttl: 4,
        },
        {
          title: "Telat Bulan Ini",
          ttl: 4,
        },
      ],
      alert: true,
      statistik: [],
      fcmToken: "",
      notif: {
        title: "",
        body: "",
      },
      title: `Dashboard`,
      vJadwalku: [],
      lembur: null,
      testNotif: "",
      menuShow: false,
    };
  },
  created() {
    if (["android", "ios"].includes(Device.getInfo().platform)) {
      this.menuShow = true;
    }
    this.jadwalkuHarian();
    // this.getQuote();
    // cek jumlah notifikasi
    if (this.isSuperAdmin) {
      this.$store.dispatch("menu/onUpdateCountLembur");
    }
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state?.auth?.data?.role === SUPER_ADMIN;
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async jadwalkuHarian() {
      this.show();
      const [err, resp] = await dashboardService.jadwal();
      if (err) {
        this.hide();
        return;
      }
      this.hide();
      this.vJadwalku = resp.data.jadwal;
      this.lembur = resp.data.lembur;
    },
    async getQuote() {
      let query = "kehidupan";
      const newQuote = await quote.Quotes(query);
      this.myQuote = newQuote;
    },
    async updateUserToken(token) {
      const userAgent = navigator.userAgent;
      let deviceType = "desktop";

      if (/Android/i.test(userAgent)) {
        deviceType = "android";
      } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
        deviceType = "ios";
      }

      await http
        .post("profil/fcm-token-update", {
          device: deviceType,
          token: token,
        })
        .then((resp) => {
          console.log(resp);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    async getTestNotif() {
      const [err, resp] = await dashboardService.fcm();
      if (err) {
        return;
      }
      this.testNotif = resp.data;
    },
  },
};
</script>
<style scoped>
.img-cust {
  position: absolute;
  bottom: 10%;
  right: 18%;
}
</style>
