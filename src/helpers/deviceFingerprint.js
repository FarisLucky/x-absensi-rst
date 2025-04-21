// src/utils/deviceId.js
import { Device } from "@capacitor/device";
import { Preferences } from "@capacitor/preferences";

export const generateDeviceFingerprint = async () => {
  // Dapatkan UUID dasar
  const { identifier } = await Device.getId();
  console.log("identifier: " + identifier);

  // Tambahkan karakteristik device
  const { model, operatingSystem } = await Device.getInfo();

  // Gabungkan dengan identifier unik
  const compositeId = `${identifier}-${model}-${operatingSystem}`;

  //   // Enkripsi SHA-256 (opsional)
  //   const encoder = new TextEncoder();
  //   const data = encoder.encode(compositeId);
  //   const hashBuffer = await crypto.subtle.digest("SHA-256", data);
  //   const hashArray = Array.from(new Uint8Array(hashBuffer));
  //   const hashHex = hashArray
  //     .map((b) => b.toString(16).padStart(2, "0"))
  //     .join("");

  // Simpan di Secure Storage
  await Preferences.configure({ group: "DEVICE_SECURE" });
  await Preferences.set({ key: "device_fingerprint", value: compositeId });
  console.log("HashHex: " + compositeId);

  return { compositeId, identifier };
};
