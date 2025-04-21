<template>
    <simplebar id="scrollbar" style="max-height: 70vh; overflow-y: auto">
        <BRow class="justify-content-between mb-1">
            <BCol cols="6">
                <h5 class="fs-14 pb-1 mb-2 border-bottom d-inline-block">
                    Progress Izin
                </h5>
            </BCol>
            <BCol cols="6">
                <div class="text-end">
                    <BButton
                        type="button"
                        variant="primary"
                        class="me-1"
                        @click="() => $refs.formIzinRef.showModal()"
                    >
                        <i class="ri-add-circle-fill me-1 align-bottom"></i>
                        Buat Izin
                    </BButton>
                    <BButton
                        variant="soft-secondary"
                        @click.prevent="getProgressIzin"
                    >
                        <i class="ri-refresh-fill"></i>
                    </BButton>
                </div>
            </BCol>
        </BRow>
        <div v-if="izinList.length < 1" class="h-100 border rounded py-4">
            <h3 class="text-center">
                Belum Ada
                <strong class="bg-primary2 text-white opacity-50 px-2 rounded">
                    Izin
                </strong>
            </h3>
        </div>
        <div
            v-else
            v-for="(izin, idx) in izinList"
            :key="idx"
            class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-primary"
        >
            <div class="accordion-item mb-1">
                <h2 class="accordion-header">
                    <button
                        class="accordion-button collapsed p-3 fw-semibold"
                        data-bs-toggle="collapse"
                        :data-bs-target="`#${izin.id}_accordion`"
                    >
                        <div class="d-flex align-items-center gap-2">
                            <strong class="fs-13 me-1">
                                <i class="ri-refresh-line text-warning"></i>
                            </strong>
                            <div class="d-inline-block">
                                <strong>
                                    {{
                                        `${izin?.day_cast}, ${izin?.mulai_cast} - ${izin.izin}: `
                                    }}
                                    <span class="p-1 bg-danger-subtle rounded">
                                        {{ `${izin.nip} - ${izin.nama}` }}
                                    </span>
                                </strong>
                            </div>
                        </div>
                    </button>
                </h2>
                <div
                    :id="izin.id + '_accordion'"
                    class="accordion-collapse collapse"
                >
                    <div class="accordion-body py-2">
                        <div class="mb-1 text-end">
                            <BButton
                                v-if="izin?.acc1_at === null"
                                class="btn btn-sm btn-soft-danger waves-effect waves-light"
                                @click.prevent="onDestroyIzin(izin.id)"
                            >
                                <i class="ri-delete-bin-2-fill"></i>
                            </BButton>
                        </div>
                        <ProgressIzin :izin="izin" :id="izin.id" />
                    </div>
                </div>
            </div>
        </div>
        <FormIzin ref="formIzinRef" @fetch="getProgressIzin" />
    </simplebar>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import FormIzin from "./FormIzin.vue";
import { izinService } from "@/services/IzinService";
import ProgressIzin from "./ProgressIzin";
import { IZIN_CUTI, IZIN_KRS } from "@/helpers/utils";
import simplebar from "simplebar-vue";
import "simplebar-vue/dist/simplebar.min.css";

export default {
    components: {
        FormIzin,
        ProgressIzin,
        simplebar,
    },
    data() {
        const user = this.$store.state?.auth?.data;

        return {
            izinList: [],
            user,
            accPihak2: false,
            IZIN_CUTI,
            IZIN_KRS,
        };
    },
    created() {
        this.getProgressIzin();
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        async getProgressIzin() {
            this.show();
            const [err, resp] = await izinService.progressByNip();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();

                return;
            }
            this.hide();
            this.izinList = resp.data;
        },
        async onAccSubmit(params) {
            if (confirm("Apakah ingin diterima ?")) {
                this.show();

                let q = queryString.stringify(params, {
                    arrayFormat: "index",
                });

                const [err] = await izinService.accSubmit(q);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();

                    return;
                }
                this.hide();
                this.getProgressIzin();
            }
        },
        async onDestroyIzin(id) {
            if (confirm("Apakah ingin menghapus ?")) {
                this.show();

                const [err] = await izinService.delete(id);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();

                    return;
                }
                this.hide();
                this.getProgressIzin();
            }
        },
    },
};
</script>
