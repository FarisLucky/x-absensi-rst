export const state = {
  reload: 0,
  filter: {
    range_tanggal: "",
    unit: null,
    status: "",
  },
};

export const mutations = {
  updateFilter(state, newProps) {
    state.filter = Object.assign({}, state.filter, newProps);
  },
};

export const actions = {
  resetFilter({ commit }) {
    commit("updateFilter", {
      range_tanggal: "",
      unit: null,
      status: "",
    });
  },

  onFilterRange({ commit }, range) {
    commit("updateFilter", { range_tanggal: range });
  },

  onFilterStatus({ commit }, status) {
    commit("updateFilter", { status: status });
  },

  onFilterUnit({ commit }, unit) {
    commit("updateFilter", { unit: unit });
  },
};
