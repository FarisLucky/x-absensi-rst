// src/utils/authCheck.js
import { App } from "@capacitor/app";
import { getDeviceFingerprint } from "@/utils/deviceFingerprint";
import { Preferences } from "@capacitor/preferences";
import { authService } from "@/services/AuthService";
import Cookies from "js-cookie";

export const checkDeviceValidity = async (user) => {
  const currentDeviceId = await getDeviceFingerprint();
  const { value: registeredDeviceId } = await Preferences.get({
    key: `user_device_${user.id}`,
  });

  if (registeredDeviceId !== currentDeviceId) {
    // Logout paksa jika device tidak sesuai
    await authService.logoutOne();
    Cookies.remove("cki-absen");
    App.exitApp(); // Tutup aplikasi (opsional)
    throw new Error("Perangkat tidak valid. Silakan login kembali.");
  }
};
