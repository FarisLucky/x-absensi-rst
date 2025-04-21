<template>
    <div style="z-index: 9999">
        <h5 v-if="isOnline" class="mb-0 text-muted">
            <i class="ri-customer-service-line"></i>
            : Online
        </h5>
        <h5 v-else class="text-danger mb-1">
            <i class="ri-close-circle-line"></i>
            : Offline
        </h5>
    </div>
</template>
  
  <script>
export default {
    data() {
        return {
            isOnline: navigator.onLine,
        };
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    beforeUnmount() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    },
    methods: {
        updateOnlineStatus() {
            this.isOnline = navigator.onLine;
        },
    },
};
</script>
  
  <style>
.offline {
    color: red;
}
</style>