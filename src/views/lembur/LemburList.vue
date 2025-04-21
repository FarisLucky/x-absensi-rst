<template>
    <BCard no-body>
        <BCardBody>
            <div class="mb-3">
                <div
                    class="d-flex justify-content-between align-items-center mb-3"
                >
                    <h5 class="pb-1 d-inline-block fs-16">Lembur</h5>
                    <router-link
                        :to="{ name: 'LemburCreate' }"
                        class="btn btn-primary"
                    >
                        <i class="ri-add-circle-fill me-1 align-bottom"></i>
                        Tambah
                    </router-link>
                </div>
                <form autocomplete="off">
                    <BRow class="g-2">
                        <BCol lg="2">
                            <div class="search-box">
                                <input
                                    :value="filter.search"
                                    type="text"
                                    class="form-control search bg-light border-light"
                                    placeholder="Cari Karyawan disini..."
                                    @input="onFilterSearchFn"
                                    @keyup.enter="fetchData"
                                />
                                <i class="ri-search-line search-icon"></i>
                            </div>
                            <small class="mb-0 text-danger">
                                <i
                                    class="ri-search-eye-line me-1 align-middle label-icon"
                                ></i>
                                Enter untuk mencari
                            </small>
                        </BCol>
                        <BCol lg="2">
                            <MonthPickerInput
                                lang="id"
                                v-model="filter_month"
                                @input="onFilterMonthFn"
                                placeholder="Pilih Bulan"
                                :clearable="true"
                                :default-month="new Date().getMonth() + 1"
                                :default-year="new Date().getFullYear()"
                            ></MonthPickerInput>
                        </BCol>
                        <BCol v-if="isSuperAdmin" lg="3">
                            <v-select
                                :modelValue="filter.unit"
                                :options="unitList"
                                :reduce="(unit) => unit.id"
                                label="nama"
                                placeholder="Pilih Unit"
                                @update:modelValue="onFilterUnitFn"
                            ></v-select>
                        </BCol>
                        <BCol>
                            <button
                                type="button"
                                class="btn btn-info btn-label waves-effect waves-light me-1"
                                @click="fetchData"
                            >
                                <i
                                    class="ri-search-eye-line me-1 align-middle label-icon"
                                ></i>
                                Cari
                            </button>
                            <BButton
                                type="button"
                                variant="success"
                                class="me-1"
                                @click="onExport"
                            >
                                <i
                                    class="ri-file-excel-2-line me-1 align-bottom"
                                ></i>
                            </BButton>
                            <BButton
                                type="button"
                                variant="outline-secondary"
                                @click="onRefresh"
                                class="me-1"
                            >
                                <i
                                    class="ri-refresh-fill me-1 align-bottom"
                                ></i>
                            </BButton>
                        </BCol>
                    </BRow>
                </form>
            </div>
            <div>
                <BRow v-if="rows.length > 0" class="g-2">
                    <BCol v-for="(list, idx) in rows" :key="idx" md="8">
                        <BCard>
                            <div class="d-flex justify-content-between">
                                <div class="flex-shrink-0">
                                    <div
                                        class="d-flex gap-2 align-items-center justify-content-center"
                                    >
                                        <div>
                                            <img
                                                v-if="list?.photo !== null"
                                                :src="list?.photo_url_cast"
                                                class="avatar-sm me-1 rounded material-shadow"
                                            />
                                            <img
                                                v-else
                                                src="@/assets/images/profil.jpg"
                                                class="avatar-sm me-1 rounded material-shadow"
                                                width="30px"
                                            />
                                        </div>
                                        <div class="p-2">
                                            <h5 class="mb-0 d-block fs-13">
                                                {{ list.nama }}
                                                <i
                                                    class="ri-information-line text-danger"
                                                    v-b-tooltip="
                                                        `Unit: ${list.unit}`
                                                    "
                                                    style="cursor: pointer"
                                                ></i>
                                            </h5>
                                            <span class="text-muted fs-11">
                                                {{ list.created_at_cast }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span
                                        :class="{
                                            'badge badge-gradient-dark':
                                                list?.status ===
                                                LEMBUR_STATUS.SELESAI,
                                            'badge badge-light text-muted':
                                                list?.status ===
                                                LEMBUR_STATUS.PENGAJUAN,
                                            'badge badge-gradient-danger':
                                                list?.status ===
                                                LEMBUR_STATUS.TOLAK,
                                        }"
                                        class="d-block mb-1"
                                    >
                                        {{ list.status_desc }}
                                    </span>
                                    <BButton
                                        v-if="
                                            list?.status ===
                                            LEMBUR_STATUS.PENGAJUAN
                                        "
                                        variant="soft-danger"
                                        size="sm"
                                        class="ms-1"
                                        @click.prevent="onDeleteLembur(list.id)"
                                    >
                                        <i class="ri-delete-bin-line"></i>
                                    </BButton>
                                </div>
                            </div>
                            <div class="mt-2 mb-3">
                                <div class="py-1">
                                    <i class="ri-calendar-line me-2 fs-13"></i>
                                    <span class="fs-12"> Tanggal: </span>
                                    <span class="fs-12 text-muted">
                                        {{ list?.tanggal_cast }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <i class="ri-time-line me-2 fs-13"></i>
                                    <span> Waktu: </span>
                                    <span class="fs-12 text-muted">
                                        {{
                                            `${list.mulai_cast} - ${list.akhir_cast}`
                                        }}
                                    </span>
                                    <span class="fs-12 text-muted">
                                        {{ ` (${list.ttl_jam} jam)` }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <i
                                        class="ri-file-list-2-line me-2 fs-13"
                                    ></i>
                                    <span class="mb-0 fs-12">
                                        Alasan:
                                        <span class="text-muted">{{
                                            list.catatan
                                        }}</span>
                                    </span>
                                </div>
                            </div>
                            <hr />
                            <div
                                class="text-end"
                                v-if="
                                    list.status === LEMBUR_STATUS.PENGAJUAN &&
                                    isSuperAdmin
                                "
                            >
                                <BButton
                                    type="button"
                                    variant="outline-danger"
                                    @click.prevent="
                                        onConfirm({
                                            id: list.id,
                                            jenis: 'TOLAK',
                                        })
                                    "
                                >
                                    <i class="ri-stop-circle-line"></i>
                                    Tolak
                                </BButton>
                                <BButton
                                    type="button"
                                    variant="success"
                                    @click.prevent="
                                        onConfirm({
                                            id: list.id,
                                            jenis: 'TERIMA',
                                        })
                                    "
                                    class="ms-1"
                                >
                                    <i class="ri-check-line"></i>
                                    Terima
                                </BButton>
                            </div>
                            <div
                                v-if="list.status !== LEMBUR_STATUS.PENGAJUAN"
                                class="d-flex justify-content-between"
                            >
                                <span>
                                    Disetujui Oleh:
                                    <span class="text-muted">
                                        {{
                                            list.lembur_approv?.acc_nama ?? "-"
                                        }}
                                    </span>
                                </span>
                                <span>
                                    Pada:
                                    <span class="text-muted">
                                        {{
                                            list.lembur_approv?.acc_at_cast ??
                                            "-"
                                        }}
                                    </span>
                                </span>
                            </div>
                        </BCard>
                    </BCol>
                    <BCol v-if="rows.length > 0" cols="12">
                        <div class="text-center">
                            <vue-awesome-paginate
                                :total-items="totalRecord"
                                :items-per-page="perPage"
                                :max-pages-shown="10"
                                v-model="currentPage"
                                :on-click="onPageChange"
                                :show-ending-buttons="true"
                            />
                        </div>
                    </BCol>
                </BRow>
                <h4 v-else class="py-4 text-center border rounded">
                    Tidak ada Data
                </h4>
            </div>
            <keterangan ref="keteranganRef" />
            <LemburDetail ref="lemburRef" />
            <BModal
                v-model="modal"
                hide-footer
                title="Tambahan Form"
                class="v-modal-custom"
                size="sm"
                no-close-on-backdrop
                no-close-on-esc
                @close="hideModal"
            >
                <div class="mb-1">
                    <label for="catatan">Catatan</label>
                    <textarea
                        class="form-control"
                        v-model="modalForm.catatan"
                        rows="3"
                        id="catatan"
                    ></textarea>
                </div>
                <div class="mb-1">
                    <BButton
                        type="button"
                        variant="primary"
                        @click.prevent="onSubmit"
                        class="w-100"
                    >
                        <i class="ri-save-2-line me-1"></i>
                        Simpan
                    </BButton>
                </div>
            </BModal>
        </BCardBody>
    </BCard>
</template>
<script>
import {
    historyLemburMethods,
    historyLemburState,
    spinnerMethods,
    toastMethods,
} from "@/state/helpers";
import queryString from "query-string";
import { LEMBUR_STATUS, SUPER_ADMIN } from "@/helpers/utils";
import { mUnitService } from "@/services/MUnitService";
import { webUrl } from "@/config/http";
import Keterangan from "@/views/izinku/Keterangan";
import LemburDetail from "@/views/history/lembur/LemburDetail";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { lemburService } from "@/services/LemburService";
import { MonthPickerInput } from "vue-month-picker";
import VueAwesomePaginate from "vue-awesome-paginate";
import "vue-awesome-paginate/dist/style.css";

export default {
    components: {
        Keterangan,
        LemburDetail,
        MonthPickerInput,
        VueAwesomePaginate,
    },
    data() {
        const user = this.$store.state?.auth?.data;

        return {
            rows: [],
            user,
            unitList: [],
            totalRecord: 0,
            currentPage: 1,
            perPage: 10,
            filter_month: "",
            LEMBUR_STATUS,
            modal: false,
            modalForm: {
                id: "",
                jenis: "",
                catatan: "",
            },
        };
    },
    directives: {
        viewer: viewer({
            debug: false,
        }),
    },
    watch: {
        reload() {
            this.fetchData();
        },
        "filter.month"(val) {
            this.filter_month = val;
        },
    },
    computed: {
        ...historyLemburState,
        isSuperAdmin() {
            return this.$store.state.auth.data.role === SUPER_ADMIN;
        },
    },
    created() {
        this.onRefresh();
        this.getUnitList();
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        ...historyLemburMethods,
        async fetchData() {
            this.show();
            this.isLoading = true;
            const query = queryString.stringify(
                Object.assign({}, this.server, this.filter),
                {
                    arrayFormat: "index",
                }
            );

            const [err, resp] = await lemburService.all(query);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;
                this.hide();

                return;
            }
            let pagination = resp.data;
            this.rows = pagination.data;
            this.totalRecords = pagination.total;
            this.perPage = parseInt(pagination.per_page);
            this.isLoading = false;
            this.hide();
        },
        onPageChange(page) {
            this.currentPage = page;
            this.fetchPosts();
        },
        onRefresh() {
            this.resetFilter();
            this.fetchData();
        },
        async getUnitList() {
            if (
                this.isSuperAdmin ||
                this.isKaSub ||
                this.isKaBid ||
                this.isDir
            ) {
                this.show();
                const [err, resp] = await mUnitService.data();
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    this.hide();
                    return;
                }
                this.unitList = resp.data;
                this.hide();
            }
        },
        getBukti(id) {
            let bukti = [];
            let file = `${webUrl}/lembur/bukti/${id}`;

            bukti.push(file);

            return bukti;
        },
        showBukti(params) {
            // const viewer = params.$viewer;
            console.log(params);
            const viewer = this.$el.querySelector(params).$viewer;
            viewer.show();
        },
        showKeterangan(params) {
            this.$refs.keteranganRef.title = params.title;
            this.$refs.keteranganRef.ket = params.ket;
            this.$refs.keteranganRef.showModal();
        },
        onFilterSearchFn(event) {
            let val = event.target.value;
            if (val.length > 2) {
                this.onFilterSearch(val);
            }
            if (val === "") {
                this.onFilterSearch("");
            }
        },
        onFilterUnitFn(val) {
            this.onFilterUnit(val);
        },
        onFilterMonthFn(val) {
            let monthly = `${val.year}-${val.monthIndex}`;
            this.filter_month = val;
            this.onFilterMonth(monthly);
        },
        showFoto(params) {
            const viewer = this.$el.querySelector(params).$viewer;
            viewer.show();
        },
        onExport() {
            let query = queryString.stringify(Object.assign(this.filter), {
                arrayFormat: "index",
            });
            let a = document.createElement("a");
            a.href = `${webUrl}/rekap/lembur/excel?${query}`;
            a.setAttribute("target", "_blank");
            a.click();
        },
        onConfirm({ id, jenis }) {
            this.$confirm({
                message: "Apakah ingin diterima ?",
                button: {
                    no: "No",
                    yes: "Yes",
                },
                callback: (confirm) => {
                    if (confirm) {
                        this.modalForm.id = id;
                        this.modalForm.jenis = jenis;
                        this.showModal();
                    }
                },
            });
        },
        showModal() {
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
            this.resetModalForm();
        },
        resetModalForm() {
            this.modalForm = {
                id: "",
                jenis: "",
                catatan: "",
            };
        },
        async onSubmit() {
            this.show();
            const [err] = await lemburService.updateStatus(this.modalForm);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();
                return;
            }
            this.fetchData();
            this.hideModal();
            this.hide();
            // cek notifikasi jumlah permintaan izin lembur
            this.$store.dispatch("menu/onUpdateCountLembur");
        },
        onDeleteLembur(id) {
            this.$confirm({
                message: "Apakah ingin dihapus ?",
                button: {
                    no: "No",
                    yes: "Yes",
                },
                callback: async (confirm) => {
                    if (confirm) {
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
                        this.toastSuccess({
                            title: "Berhasil",
                            msg: "Tindakan Berhasil",
                        });
                        this.fetchData();
                        this.hide();
                    }
                },
            });
        },
    },
};
</script>

<style>
.pagination-container {
    display: flex;

    column-gap: 10px;
}

.paginate-buttons {
    height: 40px;

    width: 40px;

    border-radius: 20px;

    cursor: pointer;

    background-color: rgb(242, 242, 242);

    border: 1px solid rgb(217, 217, 217);

    color: black;
}

.paginate-buttons:hover {
    background-color: #d8d8d8;
}

.active-page {
    background-color: #3498db;

    border: 1px solid #3498db;

    color: white;
}

.active-page:hover {
    background-color: #2988c8;
}
</style>
