<template>
    <div class="profile-timeline">
        <div class="accordion accordion-flush" id="accord">
            <div class="accordion-item border-0">
                <div class="accordion-header">
                    <a
                        class="accordion-button collapsed p-2 shadow-none"
                        data-bs-toggle="collapse"
                        :data-bs-target="'#acc1_' + lembur.id"
                        aria-expanded="true"
                    >
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-xs">
                                <div
                                    class="avatar-title bg-light rounded-circle"
                                    :class="{
                                        'text-success':
                                            lembur?.acc1_at !== null,
                                        'text-warning':
                                            lembur?.acc1_at === null,
                                    }"
                                >
                                    <i
                                        v-if="lembur?.acc1_at !== null"
                                        class="ri-checkbox-circle-fill"
                                    ></i>
                                    <i v-else class="ri-refresh-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-0">ACC Atasan</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div
                    :id="'acc1_' + lembur.id"
                    class="accordion-collapse collapse"
                >
                    <div class="accordion-body ms-2 ps-5 pt-2">
                        <h5 class="mb-1">
                            {{ `${lembur?.acc1} - ${lembur?.acc1_by?.nama}` }}
                        </h5>
                        <span
                            v-if="lembur?.acc1_at !== null"
                            class="mb-1 fs-12 text-muted"
                        >
                            <i class="ri-time-line"></i>:
                            {{ lembur?.acc1_at_cast }}
                        </span>
                        <span v-else class="mb-1">Menunggu ACC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["lembur"],
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
