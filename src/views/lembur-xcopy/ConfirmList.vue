<template>
    <div>
        <div class="d-flex justify-content-between mb-1">
            <h5 class="fs-14 pb-1 mb-2 border-bottom d-inline-block">
                Konfirmasi Pengajuan Tukar Shift
            </h5>
            <BButton
                variant="soft-secondary"
                size="sm"
                @click.prevent="getConfirmList"
            >
                <i class="ri-refresh-fill"></i>
            </BButton>
        </div>
        <div v-if="lemburList.length < 1" class="h-100 border rounded py-4">
            <h3 class="text-center">Belum Ada Permintaan Konfirmasi</h3>
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
                    <div class="accordion-body">
                        <ProgressListItem :lembur="lembur" :id="lembur.id" />

                        <BRow>
                            <BCol
                                v-if="
                                    lembur?.acc1 === user.nip &&
                                    lembur?.acc1_at === null
                                "
                                :lg="12"
                            >
                                <div class="mb-1">
                                    <p class="m-0 mb-1">Sebagai Acc 1</p>
                                </div>
                                <div class="mb-1">
                                    <BButton
                                        variant="outline-success"
                                        class="me-1"
                                        @click.prevent="
                                            onAccSubmit({
                                                type: 'acc1',
                                                id_lembur: lembur.id,
                                            })
                                        "
                                    >
                                        <i class="ri-checkbox-circle-fill"></i>
                                        Terima
                                    </BButton>
                                    <BButton
                                        variant="outline-danger"
                                        @click.prevent="
                                            showTolak({
                                                jenis: 'acc1',
                                                id_lembur: lembur.id,
                                            })
                                        "
                                    >
                                        <i class="ri-close-circle-fill"></i>
                                        Tolak
                                    </BButton>
                                </div>
                            </BCol>
                            <BCol
                                v-if="
                                    lembur?.acc2 === user.nip &&
                                    lembur?.acc1_at !== null
                                "
                                :lg="12"
                            >
                                <div class="mb-1">
                                    <p class="m-0 mb-1">Sebagai Acc 2</p>
                                </div>
                                <div class="mb-1">
                                    <BButton
                                        variant="outline-success"
                                        class="me-1"
                                        @click.prevent="
                                            onAccSubmit({
                                                type: 'acc2',
                                                id_lembur: lembur.id,
                                            })
                                        "
                                    >
                                        <i class="ri-checkbox-circle-fill"></i>
                                        Terima
                                    </BButton>
                                    <BButton
                                        variant="outline-danger"
                                        @click.prevent="
                                            showTolak({
                                                jenis: 'acc2',
                                                id_lembur: lembur.id,
                                            })
                                        "
                                    >
                                        <i class="ri-close-circle-fill"></i>
                                        Tolak
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                    </div>
                </div>
            </div>
        </div>
        <form-tolak ref="formTolakRef" @fetch="getConfirmList" />
    </div>
</template>
<script>
import FormTolak from "./FormTolak.vue";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import ProgressListItem from "./ProgressListItem.vue";
import { lemburService } from "@/services/LemburService";

export default {
    components: {
        FormTolak,
        ProgressListItem,
    },
    data() {
        const user = this.$store.state?.auth?.data;

        return {
            lemburList: [],
            user,
            accNip: 0,
            sdmNip: null,
        };
    },
    created() {
        Promise.all([this.getConfirmList()]);
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        async getConfirmList() {
            this.show();
            const [err, resp] = await lemburService.confirmList();
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

                const [err, resp] = await lemburService.accSubmit(params);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();

                    return;
                }
                this.hide();
                this.toastSuccess({
                    title: "Berhasil",
                    msg: resp.data,
                });
                this.getConfirmList();
            }
        },
        showTolak(params) {
            this.$refs.formTolakRef.form.id = params.id_lembur;
            this.$refs.formTolakRef.form.acc = params.jenis;
            this.$refs.formTolakRef.showModal();
        },
    },
};
</script>
