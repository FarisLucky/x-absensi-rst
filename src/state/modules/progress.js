export const state = {
  reload: 0,
  filter: {
    unit: null,
    search: "",
    status: "",
  },
};

export const mutations = {
  updateParams(state, newProps) {
    state.server = Object.assign({}, state.server, newProps);
  },
  updateFilter(state, newProps) {
    state.filter = Object.assign({}, state.filter, newProps);
  },
  reload(state) {
    state.reload++;
  },
};

export const actions = {
  resetFilter({ commit, dispatch }) {
    commit("updateFilter", {
      search: "",
      unit: null,
    });
    dispatch("reloadTable");
  },

  onFilterSearch({ commit, dispatch }, search) {
    commit("updateFilter", { search: search });
    dispatch("reloadTable");
  },

  onFilterUnit({ commit, dispatch }, unit) {
    commit("updateFilter", { unit: unit });
    dispatch("reloadTable");
  },

  onFilterStatus({ commit, dispatch }, status) {
    commit("updateFilter", { status: status });
    dispatch("reloadTable");
  },

  reloadTable({ commit }) {
    commit("reload");
  },
};
