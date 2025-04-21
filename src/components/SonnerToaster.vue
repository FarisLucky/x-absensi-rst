
<template>
    <Toaster position="top-right" richColors closeButton :visibleToasts="4" />
</template>

<script>
import { toastState } from "@/state/helpers";
import { Toaster, toast } from "vue-sonner";
export default {
    components: {
        Toaster,
    },
    computed: {
        ...toastState,
    },
    watch: {
        visible(newValue) {
            if (newValue) {
                toast(this.title, {
                    style: {
                        background: this.style.bg,
                        color: this.style.clr,
                    },
                    // class: this.type,
                    description: this.msg,
                });
            }
        },
    },
};
</script>