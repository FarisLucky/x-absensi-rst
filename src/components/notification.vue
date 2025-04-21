<template>
  <div style="position: relative">
    <div
      id="borderedToast4"
      class="toast fade toast-border-danger"
      :class="{ show: visible }"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      style="position: fixed; top: 16px; right: 16px; z-index: 99999"
    >
      <div class="toast-header" :class="type">
        <div class="d-flex align-items-center mb-1">
          <div class="flex-shrink-0 me-2">
            <i class="ri-alert-line align-middle text-white"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-0 text-white">
              {{ title }}
            </h6>
          </div>
        </div>
      </div>
      <div class="toast-body">
        <div class="d-flex p-2">
          <h6 class="border-1 rounded">
            {{ msg }}
          </h6>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { toastState, toastMethods } from "@/state/helpers";

export default {
  computed: {
    ...toastState,
  },
  methods: {
    ...toastMethods,
  },
  mounted() {},
};
</script>
