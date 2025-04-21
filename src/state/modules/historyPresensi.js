export const state = {
  server: {
    // a map of column filters example: {name: 'john', age: '20'}
    sort: [
      {
        field: "", // example: 'name'
        type: "", // 'asc' or 'desc'
      },
    ],

    page: 1, // what page I want to show
    perPage: 10, // how many items I'm showing per page
  },
  reload: 0,
  filter: {
    range_tanggal: "",
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
  onPageChange({ commit, dispatch }, params) {
    commit("updateParams", { page: params.currentPage });
    dispatch("reloadTable");
  },

  onPerPageChange({ commit, dispatch }, params) {
    commit("updateParams", { perPage: params.currentPerPage });
    dispatch("reloadTable");
  },

  onSortChange({ commit, dispatch }, params, columns) {
    commit("updateParams", {
      sort: [
        {
          type: params.sortType,
          field: columns[params.columnIndex].field,
        },
      ],
    });
    dispatch("reloadTable");
  },

  resetFilter({ commit }) {
    commit("updateFilter", {
      range_tanggal: "",
      unit: null,
      search: "",
      status: "",
    });
  },

  onFilterRange({ commit }, range) {
    commit("updateFilter", { range_tanggal: range });
  },

  onFilterSearch({ commit }, search) {
    commit("updateFilter", { search: search });
  },

  onFilterStatus({ commit }, status) {
    commit("updateFilter", { status: status });
  },

  onFilterUnit({ commit }, unit) {
    commit("updateFilter", { unit: unit });
  },

  reloadTable({ commit }) {
    commit("reload");
  },
};
