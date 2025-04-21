import { decryptData } from "@/helpers/encryption";
import Cookies from "js-cookie";

let dataCookies = decryptData(Cookies.get("cki-absen"));

export const state = {
  data: dataCookies?.data,
  token: dataCookies?.token,
};

export const mutations = {
  setLogin(state, message) {
    const { data, token } = message;
    state.data = data;
    state.token = token;
  },
  setLogout(state) {
    state.data = null;
    state.token = null;
  },
  setLoginByCookies(state) {
    let userCookie = dataCookies;
    state.data =
      userCookie !== undefined ? JSON.parse(userCookie).data : undefined;
    state.token =
      userCookie !== undefined ? JSON.parse(userCookie).token : undefined;
  },
};

export const actions = {
  login({ commit }, message) {
    commit("setLogin", message);
  },
  logout({ commit }) {
    commit("setLogout");
  },
  getLogin({ commit, state }) {
    if (state.data === undefined) {
      commit("setLoginByCookies");
    }

    return state;
  },
};
