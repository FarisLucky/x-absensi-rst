<style scoped>
.container-spinner {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  height: 100%;
  background: rgba(248, 250, 252, 0.323);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.spinner {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: conic-gradient(#0000 10%, #0251cb);
  -webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 9px), #000 0);
  animation: spinner-zp9dbg 1s infinite linear;
}

@keyframes spinner-zp9dbg {
  to {
    transform: rotate(1turn);
  }
}
</style>
<template>
  <div v-if="$store.state.spinner.visible" class="container-spinner">
    <div
      class="bg-white px-2 py-1 d-inline-block text-center"
      style="vertical-align: middle"
    >
      <!-- <BSpinner variant="primary" small /> -->
      <div class="spinner"></div>
      <!-- <strong class="ms-1">Loading...</strong> -->
    </div>
  </div>
</template>
<script>
export default {};
</script>
