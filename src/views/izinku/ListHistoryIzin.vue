<template>
  <div>
    <!-- right offcanvas -->
    <div
      class="offcanvas offcanvas-end"
      :class="{ show: show }"
      tabindex="-1"
      id="offcanvasRight"
      aria-labelledby="offcanvasRightLabel"
    >
      <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Offcanvas Right</h5>
        <button
          type="button"
          class="btn-close text-reset"
          data-bs-dismiss="offcanvas"
          aria-label="Close"
        ></button>
      </div>
      <div class="offcanvas-body">...</div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      show: false,
      nip: 0,
    };
  },
};
</script>
