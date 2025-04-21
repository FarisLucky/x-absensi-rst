import store from "@/state/store";

export default [
  {
    path: "/login",
    name: "Login",
    component: () => import("../views/account/login.vue"),
    meta: {
      authRequired: false,
      title: "Login",
      async beforeResolve(routeTo, routeFrom, next) {
        // If the user is already logged in

        /* GET ACTION VUEX STORE */
        let authStore = await store.dispatch("auth/getLogin");
        if (authStore.data !== undefined) {
          // Redirect to the home page instead
          next({ name: "Dashboard" });
        } else {
          // Continue to the login page
          next();
        }
      },
    },
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("../views/account/register.vue"),
    meta: {
      authRequired: false,
      title: "Register",
      beforeResolve(routeTo, routeFrom, next) {
        // If the user is already logged in
        if (store.getters["auth/loggedIn"]) {
          // Redirect to the home page instead
          next({ name: "default" });
        } else {
          // Continue to the login page
          next();
        }
      },
    },
  },
  {
    path: "/forgot-password",
    name: "Forgot password",
    component: () => import("../views/account/forgot-password.vue"),
    meta: {
      authRequired: false,
      title: "Forgot Password",
      beforeResolve(routeTo, routeFrom, next) {
        // If the user is already logged in
        if (store.getters["auth/loggedIn"]) {
          // Redirect to the home page instead
          next({ name: "default" });
        } else {
          // Continue to the login page
          next();
        }
      },
    },
  },
  {
    path: "/logout",
    name: "Logout",
    meta: {
      title: "Logout",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
  },
  {
    path: "/",
    name: "Dashboard",
    meta: {
      title: "Dashboard",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/dashboard/Index"),
  },
  {
    path: "/m-karyawan",
    name: "Karyawan",
    meta: {
      title: "Master Karyawan",
      authRequired: true,
      role: ["SUPER_ADMIN", "DIREKTUR"],
    },
    component: () => import("@/views/m_karyawan/KIndex"),
    children: [
      {
        path: "",
        name: "KaryawanList",
        meta: {
          title: "List Karyawan",
          authRequired: true,
          role: ["SUPER_ADMIN", "DIREKTUR"],
        },
        component: () => import("@/views/m_karyawan/KList"),
      },
      {
        path: "tambah",
        name: "KaryawanTambah",
        meta: {
          title: "Tambah Karyawan",
          authRequired: true,
          role: ["SUPER_ADMIN", "DIREKTUR"],
        },
        component: () => import("@/views/m_karyawan/KTambah"),
      },
      {
        path: "edit/:nip",
        name: "KaryawanEdit",
        meta: {
          title: "Edit Karyawan",
          authRequired: true,
          role: ["SUPER_ADMIN", "DIREKTUR"],
        },
        component: () => import("@/views/m_karyawan/KEditTab"),
      },
      {
        path: "e-dokumen",
        name: "KaryawanDok",
        meta: {
          title: "E Dokumen",
          authRequired: true,
          role: ["SUPER_ADMIN", "DIREKTUR"],
        },
        component: () => import("@/views/m_karyawan/edok/List"),
      },
      {
        path: "jenis-dok",
        name: "KaryawanJenisDok",
        meta: {
          title: "Jenis Dokumen",
          authRequired: true,
          role: ["SUPER_ADMIN", "DIREKTUR"],
        },
        component: () => import("@/views/m_karyawan/masdok/List"),
      },
    ],
  },
  {
    path: "/m-lokasi",
    name: "Lokasi",
    meta: { title: "Lokasi", authRequired: true, role: ["SUPER_ADMIN"] },
    component: () => import("@/views/m_lokasi/Index"),
  },
  {
    path: "/m-lembur",
    name: "MLembur",
    meta: { title: "Master Lembur", authRequired: true, role: ["SUPER_ADMIN"] },
    component: () => import("@/views/m_lembur/Index"),
  },
  {
    path: "/m-shift",
    name: "Shift",
    meta: { title: "Shift", authRequired: true, role: ["SUPER_ADMIN"] },
    component: () => import("@/views/m_shift/Index"),
  },
  {
    path: "/m-izin",
    name: "Izin",
    meta: { title: "Izin", authRequired: true, role: ["SUPER_ADMIN"] },
    component: () => import("@/views/m_izin/Index"),
  },
  {
    path: "/m-unit",
    name: "Unit",
    meta: { title: "Unit", authRequired: true, role: ["SUPER_ADMIN"] },
    component: () => import("@/views/m_unit/Index"),
  },
  {
    path: "/on-progress",
    name: "ProgressPresensi",
    meta: {
      title: "Progress Presensi",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/on_progress/Index"),
  },
  {
    path: "/presensi",
    name: "Presensi",
    meta: {
      title: "Presensi",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/presensi/Index"),
    children: [
      {
        path: "",
        name: "PresensiMain",
        meta: {
          title: "Main Presensi",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/presensi/Presensi"),
      },
      {
        path: ":id?/detail",
        name: "PresensiDetail",
        meta: {
          title: "Detail Presensi",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/presensi/PresensiDetail"),
      },
    ],
  },
  {
    path: "/harian-jadwal",
    name: "HarianJadwal",
    meta: {
      title: "Jadwal",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/harian/jadwal/Index"),
  },
  {
    path: "/harian-kerja",
    name: "HarianKerja",
    meta: {
      title: "Harian Kerja",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/jadwalku/Index"),
  },
  {
    path: "/izin-ku",
    name: "Izinku",
    meta: {
      title: "Izinku",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/izinku/Index"),
    children: [
      {
        path: "progress",
        name: "IzinkuProgress",
        meta: {
          title: "Progress Izin",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/izinku/ProgressList"),
      },
      {
        path: "confirm",
        name: "IzinkuConfirm",
        meta: {
          title: "Konfirmasi Izin",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/izinku/ConfirmIzinList"),
      },
      {
        path: "selesai",
        name: "IzinkuSelesai",
        meta: {
          title: "Selesai Izin",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/izinku/List"),
      },
    ],
  },
  {
    path: "/lembur",
    name: "Lembur",
    meta: {
      title: "Lembur",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/lembur/Index"),
    redirect: "/lembur/list",
    children: [
      {
        path: "list",
        name: "ListLembur",
        meta: {
          title: "List Lembur",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/lembur/LemburList"),
      },
      {
        path: "create",
        name: "LemburCreate",
        meta: {
          title: "Tambah Lembur",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/lembur/LemburCreate"),
      },
    ],
  },
  {
    path: "/jadwal",
    name: "Jadwal",
    meta: {
      title: "Jadwal",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/jadwal/Index"),
    redirect: "/jadwal/list",
    children: [
      {
        path: "list",
        name: "JadwalList",
        meta: {
          title: "Jadwal",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/jadwal/List"),
      },
      {
        path: "tambah",
        name: "JadwalTambah",
        meta: {
          title: "Jadwal Tambah",
          authRequired: true,
          role: ["SUPER_ADMIN", "KEPALA", "KASUB", "KABID", "DIREKTUR"],
        },
        component: () => import("@/views/jadwal/Tambah"),
      },
      {
        path: "edit/:nip?",
        name: "JadwalEdit",
        meta: {
          title: "Jadwal Edit",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/jadwal/Edit"),
      },
    ],
  },
  {
    path: "/history",
    name: "History",
    meta: {
      title: "history",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/history/Index"),
    children: [
      {
        path: "presensi",
        name: "HistoryPresensi",
        meta: {
          title: "History Presensi",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/history/presensi/Index"),
        children: [
          {
            path: "list",
            name: "HistoryPresensiList",
            meta: {
              title: "History Presensi",
              authRequired: true,
              role: [
                "KEPALA",
                "STAF",
                "SUPER_ADMIN",
                "KABID",
                "KASUB",
                "DIREKTUR",
              ],
            },
            component: () => import("@/views/history/presensi/KaryawanList"),
          },
        ],
      },
      {
        path: "jadwal",
        name: "HistoryJadwal",
        meta: {
          title: "History Jadwal",
          authRequired: true,
          role: ["SUPER_ADMIN", "KABID", "KASUB", "DIREKTUR"],
        },
        component: () => import("@/views/history/jadwal/Index"),
      },
      {
        path: "izin",
        name: "HistoryIzin",
        meta: {
          title: "History Izin",
          authRequired: true,
          role: ["KEPALA", "STAF", "SUPER_ADMIN", "KABID", "KASUB", "DIREKTUR"],
        },
        component: () => import("@/views/history/izin/Index"),
        children: [
          {
            path: "",
            name: "HistoryIzinList",
            meta: {
              title: "History Izin",
              authRequired: true,
              role: [
                "KEPALA",
                "STAF",
                "SUPER_ADMIN",
                "KABID",
                "KASUB",
                "DIREKTUR",
              ],
            },
            component: () => import("@/views/history/izin/List"),
          },
          {
            path: ":id/detail",
            name: "HistoryIzinDetail",
            meta: {
              title: "History Izin Detaill",
              authRequired: true,
              role: [
                "KEPALA",
                "STAF",
                "SUPER_ADMIN",
                "KABID",
                "KASUB",
                "DIREKTUR",
              ],
            },
            component: () => import("@/views/history/izin/ListDetail"),
          },
        ],
      },
      {
        path: "karyawan",
        name: "HistoryKaryawan",
        meta: {
          title: "History Karyawan",
          authRequired: true,
          role: ["KEPALA", "SUPER_ADMIN", "KABID", "KASUB", "DIREKTUR"],
        },
        component: () => import("@/views/history/karyawan/Index"),
        children: [
          {
            path: "",
            name: "HistoryKaryawanList",
            meta: {
              title: "List Karyawan",
              authRequired: true,
            },
            component: () => import("@/views/history/karyawan/List"),
          },
        ],
      },
      {
        path: "lembur",
        name: "HistoryLembur",
        meta: {
          title: "History Lembur",
          authRequired: true,
          role: ["STAF", "SUPER_ADMIN"],
        },
        component: () => import("@/views/history/lembur/Index"),
      },
    ],
  },
  {
    path: "/setting/perusahaan",
    name: "Companies",
    meta: {
      title: "Companies",
      authRequired: true,
      role: ["STAF", "SUPER_ADMIN"],
    },
    component: () => import("@/views/companies/Index"),
  },
  {
    path: "/rekap",
    name: "Rekap",
    meta: {
      title: "rekap",
      authRequired: true,
      role: ["SUPER_ADMIN"],
    },
    redirect: "/rekap/presensi",
    children: [
      {
        path: "presensi",
        name: "RekapPresensi",
        meta: {
          title: "Rekap Presensi",
          authRequired: true,
          role: ["SUPER_ADMIN"],
        },
        component: () => import("@/views/rekap/presensi/Index"),
      },
    ],
  },
  {
    path: "/profil/:nip/:jenis?",
    name: "ProfileKaryawan",
    meta: {
      title: "Profil Karyawan",
      authRequired: true,
      role: ["SUPER_ADMIN", "STAF", "KEPALA", "KABID", "KASUB", "DIREKTUR"],
    },
    component: () => import("@/views/profil/Profil"),
  },

  /**
   * WITHOUT LOGIN
   */
  {
    path: "/kode-red-blue/open",
    name: "cdRedBlue",
    meta: {
      title: "Kode Red&Blue RS",
      authRequired: false,
    },
    component: () => import("@/views/jadwal_code/ListOpen"),
  },
];
