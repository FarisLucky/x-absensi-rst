<template>
  <div>
    <div class="profile-timeline">
      <div class="accordion accordion-flush" id="accTimeline">
        <div class="accordion-item border-0">
          <div class="accordion-header">
            <button
              class="accordion-button collapsed p-2 shadow-none"
              data-bs-toggle="collapse"
              :data-bs-target="'#acc4_' + izin.id"
              aria-expanded="true"
            >
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 avatar-xs">
                  <div
                    class="avatar-title bg-light rounded-circle"
                    :class="{
                      'text-success': izin.acc_at !== null,
                      'text-warning': izin.acc_at === null,
                    }"
                  >
                    <i
                      v-if="izin.acc_at !== null"
                      class="ri-checkbox-circle-fill"
                    ></i>
                    <i v-else class="ri-refresh-line"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="fs-14 mb-0">ACC Manajemen</h6>
                </div>
              </div>
            </button>
          </div>
          <div
            :id="'acc4_' + izin.id"
            class="accordion-collapse collapse"
            data-bs-parent="#accTimeline"
          >
            <div class="accordion-body ms-2 ps-5 pt-2">
              <span v-if="izin.acc_at !== null" class="mb-1 fs-12">
                <i class="ri-time-line"></i>:
                {{ izin.acc_at_cast }}
              </span>
              <span v-else class="mb-1">Menunggu ACC</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["izin"],
};
</script>
<style scoped>
h6 {
  font-size: 12px !important;
}
h5 {
  font-size: 13px !important;
}
</style>
