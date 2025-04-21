import { mapState, mapActions } from "vuex";

export const layoutComputed = {
  ...mapState("layout", {
    layoutType: (state) => state.layoutType,
    sidebarSize: (state) => state.sidebarSize,
    layoutWidth: (state) => state.layoutWidth,
    topbar: (state) => state.topbar,
    mode: (state) => state.mode,
    position: (state) => state.position,
    sidebarView: (state) => state.sidebarView,
    sidebarColor: (state) => state.sidebarColor,
    sidebarImage: (state) => state.sidebarImage,
    visibility: (state) => state.visibility,
  }),
};

export const layoutMethods = mapActions("layout", [
  "changeLayoutType",
  "changeLayoutWidth",
  "changeSidebarSize",
  "changeTopbar",
  "changeMode",
  "changePosition",
  "changeSidebarView",
  "changeSidebarColor",
  "changeSidebarImage",
  "changePreloader",
  "changeVisibility",
]);

export const notificationMethods = mapActions("notification", [
  "success",
  "error",
  "clear",
]);

export const toastState = {
  ...mapState("toast", {
    visible: (state) => state.visible,
    type: (state) => state.type,
    title: (state) => state.title,
    msg: (state) => state.message,
    style: (state) => state.style,
  }),
};

export const toastMethods = mapActions("toast", [
  "toastSuccess",
  "toastError",
  "clear",
  "close",
]);

export const spinnerMethods = mapActions("spinner", ["show", "hide"]);

export const spinnerState = {
  ...mapState("spinner", {
    visible: (state) => state.visible,
  }),
};

export const historyPresensiMethods = mapActions("historyPresensi", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterRange",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterStatus",
  "resetFilter",
]);

export const historyPresensiState = {
  ...mapState("historyPresensi", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const historyIzinMethods = mapActions("historyIzin", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterRange",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterIzin",
  "resetFilter",
]);

export const historyIzinState = {
  ...mapState("historyIzin", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const rekapKehadiranMethods = mapActions("rekapKehadiran", [
  "onFilterRange",
  "onFilterUnit",
  "onFilterStatus",
  "resetFilter",
]);

export const rekapKehadiranState = {
  ...mapState("rekapKehadiran", {
    filter: (state) => state.filter,
  }),
};

export const karyawanMethods = mapActions("karyawan", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterResign",
  "resetFilter",
]);

export const karyawanState = {
  ...mapState("karyawan", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const lemburHarianMethods = mapActions("lemburHarian", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterJenis",
  "onFilterTanggal",
  "resetFilter",
]);

export const lemburHarianState = {
  ...mapState("lemburHarian", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};
export const historyLemburMethods = mapActions("historyLembur", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterJenis",
  "onFilterRange",
  "resetFilter",
]);

export const historyLemburState = {
  ...mapState("historyLembur", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const progressMethods = mapActions("progress", [
  "onFilterUnit",
  "onFilterStatus",
  "onFilterSearch",
  "resetFilter",
]);

export const progressState = {
  ...mapState("progress", {
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const authState = {
  ...mapState("auth", {
    token: (state) => state.token,
    data: (state) => state.data,
  }),
};

export const authMethods = mapActions("auth", ["login", "getLogin", "logout"]);

export const harianMethods = mapActions("harian", [
  "onPageChange",
  "onPerPageChange",
  "onSortChange",
  "onFilterTanggal",
  "onFilterUnit",
  "onFilterSearch",
  "onFilterShift",
  "resetFilter",
]);

export const harianState = {
  ...mapState("harian", {
    server: (state) => state.server,
    filter: (state) => state.filter,
    reload: (state) => state.reload,
  }),
};

export const menuMethods = mapActions("menu", ["onUpdateCountLembur"]);

export const menuState = {
  ...mapState("menu", {
    lembur: (state) => state.lembur,
  }),
};
