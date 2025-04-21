<template>
  <button v-if="showButton" @click="install" class="pwa-button">
    ⚡ Install App
  </button>
</template>

<script setup>
import { usePWAInstall } from "@/composable/usePWAInstall";
const { showButton, install } = usePWAInstall();
</script>

<style scoped>
.pwa-button {
  background: linear-gradient(135deg, #6e8efb, #a777e3);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  font-weight: bold;
}
</style>
