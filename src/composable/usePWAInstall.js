import { ref, onMounted } from "vue";

export function usePWAInstall() {
  const deferredPrompt = ref(null);
  const showButton = ref(false);

  const install = async () => {
    if (deferredPrompt.value) {
      deferredPrompt.value.prompt();
      const { outcome } = await deferredPrompt.value.userChoice;
      if (outcome === "accepted") {
        console.log("PWA installed");
      }
      deferredPrompt.value = null;
      showButton.value = false;
    }
  };

  onMounted(() => {
    window.addEventListener("beforeinstallprompt", (e) => {
      e.preventDefault();
      deferredPrompt.value = e;
      showButton.value = true;
    });
  });

  return { showButton, install };
}
