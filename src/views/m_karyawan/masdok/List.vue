<template>
    <div class="h-100">
        <BRow>
            <BCol md="8">
                <BCard no-body>
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                Master Jenis Dokumen
                            </h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <BButton
                                        variant="primary"
                                        @click.prevent="
                                            () => {
                                                modal = true;
                                            }
                                        "
                                    >
                                        <i
                                            class="ri-add-line align-bottom me-1"
                                        ></i>
                                        Tambah
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BCardHeader>
                    <BCardBody
                        class="border border-dashed border-end-0 border-start-0"
                    >
                        <BForm>
                            <BRow class="g-2">
                                <BCol xxl="4" sm="4">
                                    <div class="search-box">
                                        <input
                                            type="text"
                                            class="form-control search bg-light border-light"
                                            placeholder="Cari Jenis disini..."
                                            v-model="filter.search"
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
                                    </div>
                                </BCol>
                                <BCol>
                                    <BButton
                                        type="button"
                                        variant="outline-secondary"
                                        @click="onRefresh"
                                    >
                                        <i
                                            class="ri-refresh-fill me-1 align-bottom"
                                        ></i>
                                        Reset
                                    </BButton>
                                </BCol>
                            </BRow>
                        </BForm>
                    </BCardBody>
                    <BCardBody>
                        <div class="mb-1">
                            <vue-good-table
                                mode="local"
                                :columns="columns"
                                :rows="rows"
                                :select-options="{
                                    enabled: true,
                                    selectOnCheckboxOnly: true,
                                }"
                                :pagination-options="{
                                    enabled: true,
                                }"
                                :search-options="{
                                    enabled: true,
                                    externalQuery: filter.search,
                                }"
                                :line-numbers="true"
                                :isLoading="isLoading"
                                theme="polar-bear"
                                styleClass="vgt-table"
                            >
                                <template #table-row="props">
                                    <span v-if="props.column.field == 'action'">
                                        <a
                                            href="javascript(0)"
                                            class="btn btn-sm btn-soft-info me-1"
                                            @click.prevent="
                                                onShow(props.row.id)
                                            "
                                        >
                                            <i class="ri-play-line"></i>
                                        </a>
                                        <a
                                            class="btn btn-sm btn-soft-danger me-1"
                                            href="javascript(0)"
                                            @click.prevent="
                                                onDestroy(props.row.id)
                                            "
                                        >
                                            <i class="ri-delete-bin-6-fill"></i>
                                        </a>
                                    </span>
                                </template>
                            </vue-good-table>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <BModal
            v-model="modal"
            hide-footer
            title="Tambah Jenis Dokumen"
            title-class="exampleModalLabel"
            class="v-modal-custom"
            modal-class="zoomIn"
            centered
            header-class="p-3 bg-success-subtle"
        >
            <BForm
                autocomplete="off"
                class="needs-validation createfile-form"
                id="createfile-form"
                novalidate
            >
                <div class="mb-1">
                    <label for="jenis" class="form-label">Nama Jenis</label>
                    <input
                        type="text"
                        class="form-control"
                        id="nama"
                        v-model="modalForm.nama"
                        required
                        placeholder="Masukkan Nama Jenis"
                    />
                </div>
                <div class="hstack gap-2 justify-content-end mt-2">
                    <BButton
                        type="button"
                        variant="ghost-success"
                        id="addFileBtn-close"
                        @click="modal = false"
                    >
                        <i class="ri-close-line align-bottom"></i> Close
                    </BButton>
                    <BButton
                        type="button"
                        variant="primary"
                        id="createfile-btn"
                        @click="createNewfile"
                    >
                        Simpan
                    </BButton>
                </div>
            </BForm>
        </BModal>
    </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { toastMethods } from "@/state/helpers";
import { mJenisDokService } from "@/services/MJenisDokService";

const initFilter = () => ({
    search: "",
});

export default {
    components: {
        VueGoodTable,
    },
    data() {
        return {
            filter: initFilter(),
            columns: [
                {
                    label: "Nama",
                    field: "nama",
                },
                {
                    label: "Aksi",
                    field: "action",
                },
            ],
            rows: [],
            isLoading: false,
            modal: false,
            modalForm: {
                nama: "",
            },
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        ...toastMethods,
        async fetchData() {
            this.isLoading = true;

            const [err, resp] = await mJenisDokService.all();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            this.rows = resp.data;
            this.isLoading = false;
        },
        async onShow(id) {
            const [err, resp] = await mJenisDokService.show(id);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            this.modalForm = resp.data;
        },
        async onDestroy(id) {
            if (confirm("Apakah ingin dihapus ?")) {
                const [err] = await mJenisDokService.delete(id);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.isLoading = false;

                    return;
                }
                this.toastSuccess({
                    title: "Berhasil",
                    msg: "Tindakan berhasil",
                });
                this.fetchData();
            }
        },
        async createNewfile() {
            const [err] = await mJenisDokService.store(this.modalForm);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "Tindakan berhasil",
            });
            this.modal = false;
            this.modalForm = {
                nama: "",
            };
            this.fetchData();
        },
        onRefresh() {
            this.filter = initFilter();
            this.fetchData();
        },
    },
};
</script>
