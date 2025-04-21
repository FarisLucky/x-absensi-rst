export const state = {
  visible: false,
};

export const mutations = {
  showChange(state) {
    state.visible = true;
  },
  hideChange(state) {
    state.visible = false;
  },
};

export const actions = {
  show({ commit }) {
    commit("showChange");
  },
  hide({ commit }) {
    commit("hideChange");
  },
};
