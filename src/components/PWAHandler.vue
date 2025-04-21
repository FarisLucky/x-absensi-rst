<template>
  <div class="pwa-handler">
    <div v-if="showInstallPrompt" class="pwa-install-prompt">
      <BModal v-model="modal" title="Pasang Aplikasi" hide-footer hide-backdrop>
        <p>Pasang aplikasi untuk pengalaman lebih baik?</p>
        <button @click="installApp" class="me-1">Pasang</button>
        <button @click="showInstallPrompt = false">Nanti</button>
      </BModal>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  setup() {
    onMounted(() => {
      // Listen for service worker updates
      if ("serviceWorker" in navigator) {
        navigator.serviceWorker.addEventListener("controllerchange", () => {
          window.location.reload();
        });
      }
      modal.value = true;
    });

    // Di dalam setup()
    const checkForUpdates = () => {
      if ("serviceWorker" in navigator) {
        navigator.serviceWorker
          .register("/service-worker.js")
          .then((reg) => {
            reg.onupdatefound = () => {
              const installingWorker = reg.installing;
              installingWorker.onstatechange = () => {
                if (installingWorker.state === "installed") {
                  if (navigator.serviceWorker.controller) {
                    // SW baru tersedia

                    window.location.reload();
                  } else {
                    // SW pertama kali terinstall
                    console.log("Aplikasi siap digunakan offline");
                  }
                }
              };
            };
          })
          .catch((err) =>
            console.error("Service Worker registration failed: ", err)
          );
      }
    };

    // Panggil saat komponen mounted
    onMounted(() => {
      checkForUpdates();

      // Periksa update setiap 1 jam
      setInterval(checkForUpdates, 60 * 60 * 1000);

      window.addEventListener("beforeinstallprompt", handleBeforeInstallPrompt);

      return () => {
        window.removeEventListener(
          "beforeinstallprompt",
          handleBeforeInstallPrompt
        );
      };
    });

    // Di dalam setup()
    const deferredPrompt = ref(null);
    const showInstallPrompt = ref(false);

    const handleBeforeInstallPrompt = (e) => {
      e.preventDefault();
      deferredPrompt.value = e;
      showInstallPrompt.value = true;
    };

    const installApp = async () => {
      if (!deferredPrompt.value) return;

      deferredPrompt.value.prompt();
      const { outcome } = await deferredPrompt.value.userChoice;

      if (outcome === "accepted") {
        console.log("User menerima install prompt");
      } else {
        console.log("User menolak install prompt");
      }

      deferredPrompt.value = null;
      showInstallPrompt.value = false;
    };

    const modal = ref(false);

    return {
      showInstallPrompt,
      installApp,
      modal,
    };
  },
};
</script>

<style>
.pwa-handler {
  position: fixed;
  top: 0;
}
</style>
