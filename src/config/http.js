import axios from "axios";
import Cookies from "js-cookie";
import rateLimit from "axios-rate-limit";
import { decryptData } from "@/helpers/encryption";
import store from "@/state/store";

const url = "https://timesheet-cki.co.id/backend/api"; //WEB APACHE
const webUrl = "https://timesheet-cki.co.id/backend"; //WEB APACHE
// const url = "https://monkey-hopeful-rat.ngrok-free.app/lxkci/backend/api"; //WEB APACHE
// const webUrl = "https://monkey-hopeful-rat.ngrok-free.app/lxkci/backend"; //WEB APACHE
// const url = "http://localhost/x-absensi-cki/backend/api"; //WEB APACHE
// const url = "http://localhost/lxkci/backend/api"; //WEB APACHE
// const webUrl = "http://localhost/lxkci/backend"; //WEB APACHE

const token =
  Cookies.get("cki-absen") != undefined
    ? JSON.parse(decryptData(Cookies.get("cki-absen"))).token
    : "";

// const checkNetworkStatus = () => {
//   if (!navigator.onLine) {
//     console.log("tadek internet e");
//     throw new Error("No internet connection");
//   }
// };

const http = rateLimit(
  axios.create({
    baseURL: url,
    headers: {
      Authorization: "Bearer " + token,
      Accept: "application/json",
      "X-Device-ID": localStorage.getItem("deviceId"),
      "X-Device-TYPE": localStorage.getItem("deviceType"),
    },
    withCredentials: true,
  }),
  { maxRequests: 3, perMilliseconds: 200 }
);

http.interceptors.request.use(
  (x) => {
    // to avoid overwriting if another interceptor
    // already defined the same object (meta)
    if (!navigator.onLine) {
      store.dispatch("toast/toastError", {
        title: "Gagal",
        msg: "Tidak Ada Internet",
      });
      return;
    }

    return x;
  },
  (error) => {
    return Promise.reject(error.message);
  }
);

http.interceptors.response.use(
  (x) => {
    return x;
  },
  (error) => {
    if (error?.code === "ERR_NETWORK") {
      store.dispatch("toast/toastError", {
        title: "Gagal",
        msg: "Jaringan Tidak Stabil",
      });
    } else if (error?.response?.status === 401) {
      Cookies.remove("cki-absen");
      window.location.reload();
    }
    return Promise.reject(error);
  }
);

export { http, url, webUrl, token };
