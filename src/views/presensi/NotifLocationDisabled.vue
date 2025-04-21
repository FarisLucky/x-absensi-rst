<template>
    <BModal
        v-model="modal"
        hide-footer
        class="v-modal-custom"
        hide-header-close
        centered
        no-close-on-backdrop
        no-close-on-esc
        cancel-disabled
    >
        <div class="modal-body text-center">
            <Lottie
                colors="primary:#121331,secondary:#08a88a"
                trigger="loop"
                :options="{
                    animationData: warningAnimation,
                }"
                :height="120"
                :width="120"
            />
            <div class="mt-4">
                <h4 class="mb-3">
                    {{ msg }}
                </h4>
                <BButton
                    size="sm"
                    variant="primary"
                    @click.prevent="$emit('showMyLocation')"
                >
                    Cek Lokasi
                </BButton>
            </div>
        </div>
    </BModal>
</template>
<script>
import warningAnimation from "@/components/widgets/warning.json";
import Lottie from "@/components/widgets/lottie.vue";

export default {
    components: {
        Lottie,
    },
    props: ["msg"],
    data() {
        return {
            warningAnimation,
            modal: false,
        };
    },
    methods: {
        showModal() {
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
        },
    },
};
</script>