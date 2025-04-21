<style>
.data-karyawan {
    list-style-type: none;
    padding-left: 0;
}
.data-karyawan li span {
    display: inline-block;
    width: 5rem;
}
.data-karyawan li strong {
    padding: 0 0.3rem;
    margin-bottom: 4px;
    border-radius: 5px;
}
</style>
<template>
    <div>
        <BRow class="g-2">
            <BCol cols="12">
                <BCard no-body class="mb-1">
                    <BCardBody>
                        <BRow class="g-2">
                            <BCol class="col-6 col-md-1">
                                <div class="avatar avatar-md">
                                    <img
                                        v-if="karyawan?.photo !== null"
                                        :src="karyawan?.photo_url_cast"
                                        class="avatar-sm me-1 rounded material-shadow"
                                    />
                                    <img
                                        v-else
                                        src="@/assets/images/profil.jpg"
                                        class="avatar-sm me-1 rounded material-shadow"
                                        width="30px"
                                    />
                                </div>
                            </BCol>
                            <BCol class="col-6 col-md-2">
                                <label class="mb-1 fs-12 text-muted">
                                    NIP
                                </label>
                                <h5 class="d-block fs-13">
                                    {{ karyawan?.nip }}
                                </h5>
                            </BCol>
                            <BCol class="col-6 col-lg-3">
                                <label class="mb-1 fs-12 text-muted">
                                    Nama
                                </label>
                                <h5 class="d-block fs-13">
                                    {{ karyawan?.nama }}
                                </h5>
                            </BCol>
                            <BCol class="col-6 col-md-3">
                                <label class="mb-1 fs-12 text-muted">
                                    Lahir
                                </label>
                                <h5 class="d-block fs-13">
                                    {{
                                        karyawan?.tempat_lahir +
                                        ", " +
                                        karyawan?.tgl_lahir_cast
                                    }}
                                </h5>
                            </BCol>
                            <BCol class="col-6 col-md-3">
                                <span
                                    class="mb-1 fs-12 border-bottom text-muted"
                                    >Unit</span
                                >
                                <h5 class="d-block fs-13">
                                    {{ karyawan?.unit }}
                                </h5>
                            </BCol>
                        </BRow>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol>
                <BCard no-body>
                    <BCardBody class="border-0 h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <h5
                                class="fs-14 mb-2 d-inline-block border-bottom pb-1"
                            >
                                Data Jadwal
                            </h5>
                            <div>
                                <BButton
                                    v-if="isSuperAdmin"
                                    variant="danger"
                                    class="me-1"
                                    @click.prevent="onCreate"
                                >
                                    <i
                                        class="ri-add-line align-bottom me-1"
                                    ></i>
                                    Tambah
                                </BButton>
                                <BButton
                                    variant="outline-secondary"
                                    @click.prevent="() => $router.back()"
                                >
                                    <i class="ri-arrow-left-fill me-1"></i>
                                    Kembali
                                </BButton>
                            </div>
                        </div>
                        <BCollapse
                            id="formJadwalCollapse"
                            ref="formJadwalCollapseRef"
                        >
                            <div class="mb-2">
                                <BRow class="g-2 align-items-end">
                                    <div class="col-12">
                                        <h5
                                            class="fs-14 mb-2 d-inline-block border-bottom pb-1"
                                        >
                                            Form Jadwal
                                        </h5>
                                    </div>
                                    <div class="col-lg-12">
                                        <BFormRadioGroup
                                            id="radio-slots"
                                            v-model="form.type_tanggal"
                                            :options="[
                                                {
                                                    text: 'Harian',
                                                    value: 'harian',
                                                },
                                                {
                                                    text: 'Range',
                                                    value: 'range',
                                                },
                                            ]"
                                        >
                                        </BFormRadioGroup>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="tanggal" class="form-label">
                                            Tanggal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div
                                            v-if="
                                                form.type_tanggal === 'harian'
                                            "
                                        >
                                            <flat-pickr
                                                v-model="form.tanggal"
                                                placeholder="Pilih Tanggal"
                                                :config="{
                                                    minDate: 'today',
                                                    dateFormat: 'Y-m-d',
                                                }"
                                                class="form-control bg-light border-light"
                                            ></flat-pickr>
                                        </div>
                                        <div v-else>
                                            <flat-pickr
                                                v-model="form.tanggal"
                                                placeholder="Pilih Range Tanggal"
                                                :config="{
                                                    mode: 'range',
                                                    minDate: 'today',
                                                    dateFormat: 'Y-m-d',
                                                }"
                                                class="form-control bg-light border-light"
                                            ></flat-pickr>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label
                                            for="kode_shift"
                                            class="form-label"
                                        >
                                            Shift
                                            <span class="text-danger">*</span>
                                        </label>
                                        <v-select
                                            v-model="form.kode_shift"
                                            :options="listShift"
                                            :reduce="(st) => st.kode"
                                            label="nama"
                                            placeholder="Pilih Shift (Masuk - Pulang)"
                                        ></v-select>
                                    </div>
                                    <div class="col-lg">
                                        <BButton
                                            type="submit"
                                            variant="outline-primary"
                                            @click.prevent="onSubmit"
                                            class="me-1"
                                            :disabled="progress"
                                        >
                                            <i
                                                class="ri-save-2-line me-1 align-bottom"
                                            ></i>
                                            {{
                                                progress
                                                    ? "Tunggu Dulu"
                                                    : "Simpan"
                                            }}
                                        </BButton>
                                    </div>
                                </BRow>
                            </div>
                        </BCollapse>
                        <form autocomplete="off">
                            <BRow class="g-2 mt-2 mb-1">
                                <BCol lg="3">
                                    <div class="search-box">
                                        <input
                                            type="text"
                                            class="form-control search bg-light border-light"
                                            placeholder="Cari Jadwal disini..."
                                            v-model="filter.search"
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
                                    </div>
                                </BCol>
                                <BCol lg="2">
                                    <v-select
                                        v-model="filter.year"
                                        :options="years"
                                        placeholder="Pilih Tahun"
                                    ></v-select>
                                </BCol>
                                <BCol v-if="filter.year !== ''" lg="2">
                                    <v-select
                                        v-model="filter.month"
                                        :options="months"
                                        :reduce="(l) => l.id"
                                        label="name"
                                        placeholder="Pilih Bulan"
                                        @option:selected="onShowByNip"
                                    ></v-select>
                                </BCol>
                                <BCol>
                                    <BButton
                                        type="reset"
                                        variant="outline-secondary"
                                        @click="resetForm"
                                    >
                                        <i
                                            class="ri-refresh-fill me-1 align-bottom"
                                        ></i>
                                        Reset
                                    </BButton>
                                </BCol>
                            </BRow>
                        </form>
                        <div class="mb-1 table-responsive">
                            <vue-good-table
                                mode="local"
                                :columns="columns"
                                :rows="rows"
                                :search-options="{
                                    enabled: true,
                                    externalQuery: filter.search,
                                }"
                                :line-numbers="true"
                                :isLoading="isLoading"
                                theme="polar-bear"
                                styleClass="vgt-table striped sticky"
                            >
                                <template #table-row="props">
                                    <span v-if="props.column.field == 'libur'">
                                        <v-select
                                            v-if="
                                                (props.row.status === null &&
                                                    isCreatedBy(
                                                        props.row.created_by
                                                    ) &&
                                                    props.row.validate_at ===
                                                        null) ||
                                                isSuperAdmin
                                            "
                                            v-model="props.row.libur"
                                            :options="listLibur"
                                            :reduce="(l) => l.id"
                                            label="nama"
                                            placeholder="Pilih Libur"
                                            @option:selected="
                                                onUpdateMasuk(
                                                    {
                                                        libur: props.row.libur,
                                                    },
                                                    props.row.id
                                                )
                                            "
                                        ></v-select>
                                        <span v-else>{{
                                            props.row.libur ? "Libur" : "Masuk"
                                        }}</span>
                                    </span>
                                    <span
                                        v-if="
                                            props.column.field == 'mulai_absen'
                                        "
                                    >
                                        {{ props.row.mulai_absen }} menit
                                    </span>
                                    <span
                                        v-if="
                                            props.column.field == 'telat_masuk'
                                        "
                                    >
                                        {{ props.row.telat_masuk }} menit
                                    </span>
                                    <span
                                        v-if="
                                            props.column.field == 'telat_pulang'
                                        "
                                    >
                                        {{ props.row.telat_pulang }} menit
                                    </span>
                                    <span
                                        v-if="
                                            props.column.field == 'kode_shift'
                                        "
                                    >
                                        <form
                                            v-if="
                                                (props.row.status === null &&
                                                    isCreatedBy(
                                                        props.row.created_by
                                                    ) &&
                                                    props.row.validate_at ===
                                                        null) ||
                                                isSuperAdmin
                                            "
                                            autocomplete="off"
                                        >
                                            <v-select
                                                v-model="props.row.kode_shift"
                                                :options="listShift"
                                                :reduce="(st) => st.kode"
                                                label="nama"
                                                placeholder="Pilih Shift (Masuk - Pulang)"
                                                @option:selected="
                                                    onUpdateJadwal(
                                                        {
                                                            kode_shift:
                                                                props.row
                                                                    .kode_shift,
                                                        },
                                                        props.row.id
                                                    )
                                                "
                                                :disabled="props.row.libur"
                                            ></v-select>
                                        </form>
                                        <span v-else>
                                            <div class="mb-1 text-center">
                                                <span>{{
                                                    props.row.shift
                                                }}</span>
                                            </div>
                                            <div class="mb-1 text-center">
                                                <strong>
                                                    {{
                                                        `${props.row.jam_masuk} - ${props.row.jam_pulang}`
                                                    }}
                                                </strong>
                                            </div>
                                        </span>
                                    </span>
                                    <span
                                        v-if="props.column.field == 'status'"
                                        class="badge badge-info"
                                    >
                                        <span
                                            v-if="
                                                props.row.status != null &&
                                                props.row?.status_absen !== null
                                            "
                                        >
                                            <strong class="py-1 rounded">
                                                {{ props.row.status_absen }}
                                                <span>
                                                    {{
                                                        `(${
                                                            props.row.masuk
                                                        } - ${
                                                            props.row.pulang ??
                                                            ""
                                                        })`
                                                    }}
                                                </span>
                                            </strong>
                                        </span>
                                        <span v-else-if="props.row.libur == 1">
                                            LIBUR
                                        </span>
                                        <span v-else-if="props.row.status == 3">
                                            ALPA
                                        </span>
                                        <span v-else-if="props.row.status == 4">
                                            IZIN
                                        </span>
                                    </span>
                                    <span v-if="props.column.field == 'action'">
                                        <BButton
                                            v-if="
                                                (props.row.status === null &&
                                                    isCreatedBy(
                                                        props.row.created_by
                                                    ) &&
                                                    props.row.validate_at ===
                                                        null) ||
                                                isSuperAdmin
                                            "
                                            variant="soft-danger"
                                            size="sm"
                                            @click.prevent="
                                                onDelete(props.row.id)
                                            "
                                        >
                                            <i class="ri-delete-bin-fill"></i>
                                        </BButton>
                                    </span>
                                </template>
                            </vue-good-table>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { toastMethods } from "@/state/helpers";
import { SUPER_ADMIN } from "@/helpers/utils";
import flatPickr from "vue-flatpickr-component";
import { mShiftService } from "@/services/MShiftService";
import { jadwalService } from "@/services/JadwalService";
import { mKaryawanService } from "@/services/MKaryawanService";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { months, getYears } from "@/helpers/utils";

const initForm = () => {
    return {
        id: "",
        nip: "",
        tanggal: "",
        kode_shift: "",
        type_tanggal: "harian",
    };
};

export default {
    components: {
        VueGoodTable,
        flatPickr,
    },
    setup() {
        return { v$: useVuelidate() };
    },
    data() {
        const currentTime = new Date();
        return {
            form: initForm(),
            submitted: false,
            dataEdit: false,
            listShift: [],
            listKaryawan: [],
            flatConf: {
                minDate: "today",
                dateFormat: "Y-m-d",
            },
            jadwals: [],
            filter: {
                search: "",
                month: currentTime.getMonth() + 1,
                year: currentTime.getFullYear(),
            },
            karyawan: null,
            listLibur: [
                {
                    id: 1,
                    nama: "Libur",
                },
                {
                    id: 0,
                    nama: "Masuk",
                },
            ],
            progress: false,
            columns: [
                {
                    label: "Tanggal",
                    field: "tanggal_cast",
                },
                {
                    label: "Libur",
                    field: "libur",
                },
                {
                    label: "Shift",
                    field: "kode_shift",
                },
                {
                    label: "Mulai",
                    field: "mulai_absen",
                },
                {
                    label: "Terlambat",
                    field: "telat_masuk",
                },
                {
                    label: "Absen Pulang",
                    field: "telat_pulang",
                },
                {
                    label: "Status",
                    field: "status",
                },
                {
                    label: "Pembuat",
                    field: "action",
                    tdClass: this.tdClassFun,
                },
            ],
            rows: [],
            isLoading: false,
            selected: [],
            months,
            years: [],
        };
    },
    validations() {
        return {
            form: {
                tanggal: { required },
                kode_shift: { required },
            },
        };
    },
    watch: {
        "filter.search"(newValue) {
            if (newValue !== "") {
                this.jadwals = this.jadwals.filter((jadwal) =>
                    jadwal.includes(newValue)
                );
            } else {
                this.onShowByNip();
            }
        },
    },
    computed: {
        tanggalConf() {
            return this.form.type_tanggal === "harian";
        },
        isSuperAdmin() {
            return this.$store.state.auth.data.role === SUPER_ADMIN;
        },
    },
    created() {
        this.form.nip = this.$route.params.nip;
        Promise.all([this.getShift(), this.getKaryawan(), this.onShowByNip()]);
        this.years = getYears();
    },
    methods: {
        ...toastMethods,
        tdClassFun(row) {
            let msg = "";
            if (row?.presensi?.status === "TEPAT") {
                msg += "bg-primary-cs";
            } else if (row?.presensi?.status === "TELAT") {
                msg += "bg-danger bg-opacity-50";
            } else if (row.libur == 1) {
                msg += "bg-secondary-subtle";
            } else if (row.status == 3) {
                msg += "bg-warning-subtle";
            } else if (row.status == 4) {
                msg += "bg-success-subtle";
            } else if (row.status == 6) {
                msg += "bg-info-subtle";
            }

            return msg;
        },

        async onShowByNip() {
            this.resetJadwal();
            this.isLoading = true;
            const [err, resp] = await jadwalService.showByNip({
                nip: this.$route.params.nip,
                month: this.filter.month,
                year: this.filter.year,
            });
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;
                return;
            }
            this.isLoading = false;
            this.rows = resp.data;
        },
        async getShift() {
            const [err, resp] = await mShiftService.data();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                return;
            }
            this.listShift = resp.data;
        },

        async getKaryawan() {
            const [err, resp] = await mKaryawanService.show(
                this.$route.params.nip
            );
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                return;
            }
            this.karyawan = resp.data;
        },

        async onDelete(id) {
            if (confirm("Hapus jadwal ?")) {
                const [err] = await jadwalService.delete(id);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    return;
                }
                this.onShowByNip();
            }
        },

        async onDestroyJadwal() {
            if (confirm("Hapus jadwal ?")) {
                let ids = this.selected.map((jadwal) => jadwal.id);
                const [err] = await jadwalService.deleteAll(ids);
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });
                    return;
                }
                this.onShowByNip();
            }
        },

        async onSubmit() {
            const result = await this.v$.$validate();
            if (!result) {
                this.toastError({
                    title: "Gagal",
                    msg: "Form wajib diisi",
                });
                return;
            }
            this.progress = true;

            const [err] = await jadwalService.store(this.form);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.progress = false;
                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "OK",
            });
            this.resetForm();
            this.$emit("close");
            this.progress = false;
        },

        resetForm() {
            const currentTime = new Date();
            this.$data.form = initForm();
            this.$data.form.nip = this.$route.params.nip;
            this.$data.filter = {
                search: "",
                month: currentTime.getMonth() + 1,
                year: currentTime.getFullYear(),
            };
            this.onShowByNip();
        },
        resetJadwal() {
            this.rows = [];
        },
        onCreate() {
            if (this.$refs.formJadwalCollapseRef.visible) {
                this.closeForm();
                return;
            }
            this.openForm();
        },
        openForm() {
            this.$refs.formJadwalCollapseRef.open();
        },
        closeForm() {
            this.$refs.formJadwalCollapseRef.close();
        },
        async onUpdateJadwal(form, id) {
            const [err] = await jadwalService.updateShift(form, id);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "OK",
            });
        },
        async onUpdateMasuk(form, id) {
            const [err] = await jadwalService.updateLibur(form, id);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "OK",
            });
        },
        onSelect(params) {
            this.selected = params.selectedRows;
        },
        isCreatedBy(nip) {
            return this.$store.state.auth.data.nip === nip;
        },
    },
};
</script>
