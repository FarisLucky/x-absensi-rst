export const state = {
  visible: false,
  type: null,
  message: null,
};

export const mutations = {
  success(state, message) {
    const { title, msg } = message;
    state.visible = true;
    state.type = "bg-soft-success";
    state.title = title;
    state.style = {
      bg: "#fafafa",
      clr: "#0a0a0a",
    };
    state.message = msg;
  },
  error(state, message) {
    const { title, msg } = message;
    state.visible = true;
    state.type = "bg-danger";
    state.style = {
      bg: "#fafafa",
      clr: "#0a0a0a",
    };
    state.title = title;
    state.message = msg;
  },
  clear(state) {
    state.visible = true;
    state.type = null;
    state.title = null;
    state.message = null;
  },
  close(state) {
    state.visible = false;
    state.type = null;
    state.title = null;
    state.message = null;
  },
};

export const actions = {
  toastSuccess(context, message) {
    context.commit("success", message);
    context.dispatch("close");
  },
  toastError({ commit, dispatch }, message) {
    commit("error", message);
    dispatch("close");
  },
  clear({ commit, dispatch }) {
    commit("clear");
    dispatch("close");
  },
  close({ commit }) {
    setTimeout(() => {
      commit("close");
    }, 3000);
  },
};
