<template>
    <BModal
        v-model="modal"
        hide-footer
        title="Live Update"
        class="v-modal-custom"
        no-close-on-backdrop
        no-close-on-esc
        no-header-close
    >
        <div>
            <h2>Cek & Update Aplikasi</h2>
            <small class="text-danger">
                * membutuhkan waktu untuk pembaruan. diharap jangan menutup
                aplikasi
            </small>
            <button @click="checkForUpdate" class="btn btn-info">
                <i class="ri-refresh-line me-1"></i>
                Cek Untuk Pembaruan
            </button>
            <div v-if="updateAvailable" class="alert alert-success">
                <!-- <p>A new update is available!</p> -->
                <p>Sebuah pembaruan baru sudah tersedia</p>
            </div>
        </div>
    </BModal>
</template>
  
  <script>
import { LiveUpdate } from "@capawesome/capacitor-live-update";
import { url } from "@/config/http";
import { spinnerMethods } from "@/state/helpers";

export default {
    data() {
        return {
            updateAvailable: false,
            modal: false,
        };
    },
    methods: {
        ...spinnerMethods,
        showModal() {
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
        },
        async checkForUpdate() {
            try {
                // Periksa pembaruan dari server
                this.show();
                const result = await LiveUpdate.checkForUpdate({
                    url: url + "/check-update", // URL API untuk memeriksa pembaruan
                });

                if (result.updateAvailable) {
                    this.updateAvailable = true;
                    await this.downloadAndApplyUpdate(result.downloadUrl);
                    this.hide();
                } else {
                    alert("Your app is up to date!");
                    this.hide();
                }
            } catch (error) {
                console.error("Error checking for update:", error);
            }
        },
        async downloadAndApplyUpdate(downloadUrl) {
            try {
                // Unduh dan terapkan pembaruan
                await LiveUpdate.downloadUpdate({
                    url: downloadUrl, // URL untuk mengunduh file pembaruan (ZIP)
                });

                // Terapkan pembaruan
                await LiveUpdate.applyUpdate();

                // Restart aplikasi
                await LiveUpdate.reloadApp();
            } catch (error) {
                console.error("Error downloading or applying update:", error);
            }
        },
    },
};
</script>