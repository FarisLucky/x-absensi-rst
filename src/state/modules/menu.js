import { izinService } from "@/services/IzinService";
import { lemburService } from "@/services/LemburService";
import store from "../store";

export const state = {
  menu: {
    lembur: null,
    izin: null,
  },
};

export const mutations = {
  update(state, newProps) {
    state.menu = Object.assign({}, state.menu, newProps);
  },
};

export const actions = {
  async onUpdateCountLembur({ commit, dispatch }) {
    const lembur = await dispatch("getLemburNeedApproval");
    const izin = await dispatch("getIzinNeedApproval");
    commit("update", { lembur: lembur, izin: izin });
  },
  async getLemburNeedApproval() {
    const [err, resp] = await lemburService.needApproval();
    if (err) {
      store.dispatch("toast/toastError", {
        title: "Gagal",
        msg: err.response?.data?.errors,
      });
      return;
    }
    return resp.data;
  },
  async getIzinNeedApproval() {
    const [err, resp] = await izinService.needApproval();
    if (err) {
      store.dispatch("toast/toastError", {
        title: "Gagal",
        msg: err.response?.data?.errors,
      });
      return;
    }
    return resp.data;
  },
};
