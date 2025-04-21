<template>
    <div>
        <BRow class="mb-1">
            <BCol :md="12">
                <div class="text-end">
                    <BButton
                        type="button"
                        variant="soft-primary"
                        class="me-1"
                        @click="() => $refs.formLemburRef.showModal()"
                    >
                        <i class="ri-add-circle-fill me-1 align-bottom"></i>
                        Buat Lembur
                    </BButton>
                    <BButton
                        variant="soft-secondary"
                        @click.prevent="getProgressLembur"
                    >
                        <i class="ri-refresh-fill"></i>
                    </BButton>
                </div>
            </BCol>
        </BRow>
        <div v-if="lemburList.length < 1" class="h-100 border rounded py-4">
            <h3 class="text-center">
                Belum Ada
                <strong class="bg-primary2 text-white opacity-50 px-2 rounded">
                    Lembur
                </strong>
            </h3>
        </div>
        <div
            v-else
            v-for="(lembur, idx) in lemburList"
            :key="idx"
            class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-primary"
        >
            <div class="accordion-item mb-1">
                <h2 class="accordion-header">
                    <button
                        class="accordion-button collapsed p-3 fw-semibold"
                        data-bs-toggle="collapse"
                        :data-bs-target="`#${lembur.id}_accordion`"
                    >
                        <div class="d-flex align-items-center gap-2">
                            <strong class="fs-13 me-1">
                                <i
                                    class="ri-compass-discover-line text-primary fs-12"
                                ></i>
                            </strong>
                            <div class="d-inline-block">
                                <strong>
                                    {{
                                        `${lembur.tanggal_cast} ${lembur?.lembur}`
                                    }}
                                    :
                                    <span
                                        class="p-1 bg-danger-subtle rounded me-2"
                                    >
                                        {{ lembur.nama }}
                                    </span>
                                    <span
                                        v-if="lembur.parent_id !== null"
                                        class="badge badge-gradient-success"
                                    >
                                        Anggota
                                    </span>
                                </strong>
                            </div>
                        </div>
                    </button>
                </h2>
                <div
                    :id="lembur.id + '_accordion'"
                    class="accordion-collapse collapse"
                >
                    <div class="accordion-body py-1">
                        <div class="mb-1 text-end">
                            <BButton
                                v-if="lembur?.acc1_at === null"
                                class="btn btn-sm btn-soft-danger waves-effect waves-light"
                                @click.prevent="onDestroy(lembur.id)"
                            >
                                <i class="ri-delete-bin-2-fill"></i>
                            </BButton>
                        </div>
                        <ProgressListItem :lembur="lembur" :id="lembur.id" />
                    </div>
                </div>
            </div>
        </div>
        <FormLembur ref="formLemburRef" @fetch="getProgressLembur" />
    </div>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import ProgressListItem from "./ProgressListItem.vue";
import FormLembur from "./FormLembur.vue";
import { lemburService } from "@/services/LemburService";

export default {
    components: {
        ProgressListItem,
        FormLembur,
    },
    data() {
        const user = this.$store.state?.auth?.data;

        return {
            lemburList: [],
            user,
            accPihak2: false,
        };
    },
    created() {
        this.getProgressLembur();
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        async getProgressLembur() {
            this.show();
            const [err, resp] = await lemburService.progressByNip();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();

                return;
            }
            this.hide();
            this.lemburList = resp.data;
        },
        async onAccSubmit(params) {
            if (confirm("Apakah ingin diterima ?")) {
                this.show();

                let q = queryString.stringify(params, {
                    arrayFormat: "index",
                });

                const [err] = await lemburService.accSubmit(q);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();

                    return;
                }
                this.hide();
                this.getProgressLembur();
            }
        },
        async onDestroy(id) {
            if (confirm("Apakah ingin menghapus ?")) {
                this.show();

                const [err] = await lemburService.destroy(id);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();

                    return;
                }
                this.hide();
                this.getProgressLembur();
            }
        },
    },
};
</script>
