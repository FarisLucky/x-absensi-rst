import { createApp, h } from "vue";
import App from "./App.vue";
import router from "./router";
import i18n from "./i18n";
import store from "./state/store";

import BootstrapVueNext from "bootstrap-vue-next";

import VueSelect from "vue-select";
import bottomNavigationVue from "bottom-navigation-vue";
import Vue3ConfirmDialog from "vue3-confirm-dialog";

import "vue3-confirm-dialog/style";
import "bottom-navigation-vue/dist/style.css";
import "@/assets/scss/config/default/app.scss";
import "leaflet/dist/leaflet.css";
import "@/assets/scss/mermaid.min.css";
import "bootstrap/dist/js/bootstrap.bundle";
import "vue-good-table-next/dist/vue-good-table-next.css";
import "./registerServiceWorker";

VueSelect.props.components.default = () => ({
  Deselect: {
    render: () => h("span", { class: "fs-10" }, "✖"),
  },
  OpenIndicator: {
    render: () => h("span", { class: "fs-10" }, "▼"),
  },
});

const app = createApp(App)
  .use(store)
  .use(router)
  .use(BootstrapVueNext)
  .use(i18n)
  .use(bottomNavigationVue)
  .use(Vue3ConfirmDialog);

app
  .component("v-select", VueSelect)
  .component("vue3-confirm-dialog", Vue3ConfirmDialog.default);

app.mount("#app");
