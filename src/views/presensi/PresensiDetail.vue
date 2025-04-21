<template>
    <BRow class="p-1 justify-content-center">
        <BCol md="8">
            <BCard no-body>
                <BCardBody>
                    <form @submit.prevent="onSubmit">
                        <div class="px-2">
                            <div class="py-1">
                                <div class="d-flex ms-2">
                                    <div class="flex-shrink-0">
                                        <div
                                            v-if="
                                                presensi?.m_karyawan?.photo !==
                                                null
                                            "
                                            class="rounded-3 avatar-md border border-secondary"
                                        >
                                            <BLink
                                                @click.prevent="
                                                    showBukti(
                                                        '.img-' + presensi?.nip
                                                    )
                                                "
                                            >
                                                <img
                                                    :src="
                                                        presensi?.m_karyawan
                                                            ?.photo_url_cast
                                                    "
                                                    alt="karyawan-img"
                                                    class="img-fluid"
                                                />
                                            </BLink>
                                            <div
                                                class="images d-none"
                                                :class="'img-' + presensi?.nip"
                                                v-viewer="{ movable: false }"
                                            >
                                                <img
                                                    :src="
                                                        presensi?.m_karyawan
                                                            ?.photo_url_cast
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div v-else class="avatar-md rounded">
                                            <img
                                                src="@/assets/images/profil.jpg"
                                                class="member-img img-fluid d-block rounded"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div
                                            class="overflow-hidden mt-1 text-muted mb-1"
                                            style="height: 20px"
                                        >
                                            <h5
                                                class="fs-16 mb-1 overflow-hidden"
                                            >
                                                {{ presensi?.nama }}
                                            </h5>
                                        </div>
                                        <div class="badge badge-gradient-info">
                                            {{ presensi.unit }}
                                        </div>
                                    </div>
                                    <div>
                                        <span
                                            class="fs-12"
                                            :class="{
                                                'badge badge-gradient-secondary':
                                                    presensi.status === null,
                                                'badge badge-gradient-info':
                                                    presensi.status ===
                                                    JADWAL_STATUS.PROGRESS,
                                                'badge badge-gradient-primary':
                                                    presensi.status ===
                                                    JADWAL_STATUS.SELESAI,
                                                'badge badge-gradient-danger':
                                                    presensi.status ===
                                                    JADWAL_STATUS.TIDAK_HADIR,
                                                'badge badge-gradient-warning':
                                                    presensi.status ===
                                                    JADWAL_STATUS.IZIN,
                                            }"
                                        >
                                            {{ presensi.status_cast }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pb-2 pt-3">
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <h5 class="mb-1">Jadwal</h5>
                                    </div>
                                    <BRow class="g-2">
                                        <BCol md="4">
                                            <label for="tanggal">Tanggal</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        <i
                                                            class="ri-calendar-event-line"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="`${presensi.day_cast}, ${presensi?.tanggal_cast}`"
                                                    id="tanggal"
                                                    disabled
                                                />
                                            </div>
                                        </BCol>
                                        <BCol md="4">
                                            <label for="masuk">Jam Masuk</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        <i
                                                            class="ri-time-line"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="masuk"
                                                    :value="`${presensi.jam_masuk} - ${presensi.jam_pulang}`"
                                                    disabled
                                                />
                                            </div>
                                        </BCol>
                                        <BCol md="4">
                                            <label for="shift">Shift</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        <i
                                                            class="ri-swap-line"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    :value="presensi.shift"
                                                    id="shift"
                                                    disabled
                                                />
                                            </div>
                                        </BCol>
                                    </BRow>
                                </div>
                            </div>
                            <div class="pb-2 pt-3">
                                <BRow class="g-2">
                                    <BCol cols="6">
                                        <div class="rounded px-3 text-center">
                                            <h5 class="mb-1">
                                                Total Terlambat
                                            </h5>
                                            <h4 class="mb-1">
                                                {{ presensi?.ttltelat }} M
                                            </h4>
                                        </div>
                                    </BCol>
                                    <BCol cols="6">
                                        <div class="rounded px-3 text-center">
                                            <h5 class="mb-1">
                                                Total Jam Kerja
                                            </h5>
                                            <h4 class="mb-1">
                                                {{
                                                    convertMinutes(
                                                        presensi?.ttlkerja
                                                    )
                                                }}
                                            </h4>
                                        </div>
                                    </BCol>
                                </BRow>
                            </div>
                            <div class="pb-2 pt-3">
                                <h5 class="mb-2">Presensi</h5>
                                <BRow class="g-2 align-items-end">
                                    <BCol cols="6" md="3">
                                        <label>Masuk</label>
                                        <flat-pickr
                                            v-model="presensi.tanggal"
                                            :config="{
                                                altInput: true,
                                                altFormat: 'd-m-Y',
                                                dateFormat: 'Y-m-d',
                                            }"
                                            class="form-control"
                                            required
                                            placeholder="Tanggal Masuk"
                                            :disabled="
                                                !isSuperAdmin ||
                                                ![
                                                    JADWAL_STATUS.PROGRESS,
                                                    JADWAL_STATUS.SELESAI,
                                                ].includes(presensi.status)
                                            "
                                        ></flat-pickr>
                                    </BCol>
                                    <BCol cols="6" md="3">
                                        <label>Jam Masuk</label>
                                        <flat-pickr
                                            v-model="presensi.masuk"
                                            :config="{
                                                enableTime: true,
                                                noCalendar: true,
                                                dateFormat: 'H:i',
                                                time_24hr: true,
                                            }"
                                            class="form-control"
                                            required
                                            placeholder="Jam Masuk"
                                            :disabled="
                                                !isSuperAdmin ||
                                                ![
                                                    JADWAL_STATUS.PROGRESS,
                                                    JADWAL_STATUS.SELESAI,
                                                ].includes(presensi.status)
                                            "
                                        ></flat-pickr>
                                    </BCol>
                                    <BCol cols="6" md="3">
                                        <label>Tgl Pulang</label>
                                        <flat-pickr
                                            v-model="presensi.tgl_pulang"
                                            :config="{
                                                altInput: true,
                                                altFormat: 'd-m-Y',
                                                dateFormat: 'Y-m-d',
                                            }"
                                            class="form-control"
                                            required
                                            placeholder="Tanggal Pulang"
                                            :disabled="
                                                !isSuperAdmin ||
                                                JADWAL_STATUS.SELESAI !==
                                                    presensi.status
                                            "
                                        ></flat-pickr>
                                    </BCol>
                                    <BCol cols="6" md="3">
                                        <label>Jam Pulang</label>
                                        <flat-pickr
                                            v-model="presensi.pulang"
                                            :config="{
                                                enableTime: true,
                                                noCalendar: true,
                                                dateFormat: 'H:i',
                                                time_24hr: true,
                                            }"
                                            class="form-control"
                                            placeholder="Jam Pulang"
                                            required
                                            :disabled="
                                                !isSuperAdmin ||
                                                JADWAL_STATUS.SELESAI !==
                                                    presensi.status
                                            "
                                        ></flat-pickr>
                                    </BCol>
                                    <BCol cols="6" md="3">
                                        <label>Status Absen</label>
                                        <v-select
                                            :modelValue="presensi.status"
                                            :options="statusList"
                                            @update:modelValue="selectStatus"
                                            placeholder="Pilih Status"
                                            :reduce="(status) => status.id"
                                            label="nama"
                                            :disabled="!isSuperAdmin"
                                        ></v-select>
                                    </BCol>
                                    <BCol cols="6" md="3">
                                        <label>Status Keterlambatan</label>
                                        <v-select
                                            v-model="presensi.status_absen"
                                            :options="['TEPAT', 'TELAT']"
                                            placeholder="Pilih Status"
                                            :disabled="!isSuperAdmin"
                                        ></v-select>
                                    </BCol>
                                    <BCol
                                        v-if="
                                            presensi.status ===
                                            JADWAL_STATUS.SELESAI
                                        "
                                        cols="6"
                                        md="4"
                                    >
                                        <label>Cara Pulang</label>
                                        <v-select
                                            v-model="presensi.auto"
                                            :options="[
                                                {
                                                    id: null,
                                                    nama: 'CHECKOUT USER',
                                                },
                                                {
                                                    id: 1,
                                                    nama: 'OTOMATIS SYSTEM',
                                                },
                                            ]"
                                            :reduce="(auto) => auto.id"
                                            label="nama"
                                            placeholder="Pilih Cara Pulang"
                                            :disabled="!isSuperAdmin"
                                        ></v-select>
                                    </BCol>
                                    <BCol
                                        v-if="presensi.status_absen === 'TELAT'"
                                        cols="6"
                                        md="5"
                                    >
                                        <label>Keterangan Terlambat</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="
                                                presensi.presensi_terlambat.ket
                                            "
                                            :disabled="!isSuperAdmin"
                                        />
                                    </BCol>
                                    <BCol v-if="isSuperAdmin">
                                        <div>
                                            <BButton
                                                type="submit"
                                                variant="primary"
                                            >
                                                <i class="ri-save-3-line"></i>
                                                Simpan
                                            </BButton>
                                        </div>
                                    </BCol>
                                </BRow>
                            </div>
                            <BAccordion
                                v-if="presensi?.presensi !== null"
                                class="custom-accordionwithicon"
                                flush
                                id="accordionFlushExample"
                            >
                                <BAccordionItem
                                    headerClass="p-0"
                                    buttonClass="py-2 px-0"
                                >
                                    <template #title>
                                        <h5 class="mb-1">Detail Presensi</h5>
                                    </template>
                                    <BRow>
                                        <BCol cols="6">
                                            <ul style="list-style-type: none">
                                                <li>Pabrik</li>
                                                <li>Model Perangkat</li>
                                                <li>Platform</li>
                                                <li>OS Version</li>
                                                <li>IP</li>
                                            </ul>
                                        </BCol>
                                        <BCol cols="6">
                                            <ul style="list-style-type: none">
                                                <li>
                                                    {{
                                                        presensi.presensi
                                                            ?.manufact
                                                    }}
                                                </li>
                                                <li>
                                                    {{
                                                        presensi.presensi?.model
                                                    }}
                                                </li>
                                                <li>
                                                    {{
                                                        presensi.presensi
                                                            ?.platform
                                                    }}
                                                </li>
                                                <li>
                                                    {{
                                                        presensi.presensi
                                                            ?.osVersion
                                                    }}
                                                </li>
                                                <li>
                                                    {{ presensi.presensi?.ip }}
                                                </li>
                                            </ul>
                                        </BCol>
                                    </BRow>
                                </BAccordionItem>
                            </BAccordion>
                        </div>
                    </form>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
</template>
<script>
import animationData4 from "@/components/widgets/pithnlch.json";
import { JADWAL_STATUS, SUPER_ADMIN, TELAT, TEPAT } from "@/helpers/utils";
import { jadwalService } from "@/services/JadwalService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { convertMinutesToHours } from "@/helpers/format";
import dayjs from "dayjs";

const initForm = () => ({
    id: "",
});

export default {
    components: {
        flatPickr,
    },
    data() {
        let lists = Object.entries(JADWAL_STATUS).map((status) => {
            return {
                id: status[1],
                nama: status[0],
            };
        });
        return {
            animationData4,
            form: initForm(),
            modal: false,
            presensi: {
                id: "",
                masuk: "",
                tanggal: "",
                tgl_pulang: "",
                pulang: "",
                status: 0,
                status_absen: "",
                presensi_terlambat: {
                    ket: "",
                },
                ttltelat: 0,
                ttlkerja: 0,
            },
            TELAT,
            TEPAT,
            JADWAL_STATUS,
            statusList: lists,
        };
    },
    directives: {
        viewer: viewer({
            debug: false,
        }),
    },
    computed: {
        isSuperAdmin() {
            return this.$store.state.auth.data.role === SUPER_ADMIN;
        },
    },
    watch: {
        "presensi.pulang"(newValue) {
            console.log(this.presensi.masuk);
            if (this.presensi.masuk !== null) {
                // tgl pulang kosong atau tidak
                if (
                    this.presensi.tgl_pulang === null &&
                    this.presensi.pulang !== null
                ) {
                    this.presensi.tgl_pulang = this.presensi.tanggal;
                }

                if (this.presensi.status === JADWAL_STATUS.SELESAI) {
                    let masukDateTime = dayjs(
                        `${this.presensi.tanggal} ${this.presensi.masuk}`
                    );
                    let pulangDateTime = dayjs(
                        `${this.presensi.tgl_pulang} ${newValue}`
                    );
                    let ttlKerja = pulangDateTime.diff(
                        masukDateTime,
                        "minutes"
                    );

                    this.presensi.ttlkerja = ttlKerja;
                }
            }
        },
    },
    created() {
        this.form.id = this.$route.params.id;
        this.showPresensi();
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        async showPresensi() {
            this.show();
            const [err, resp] = await jadwalService.show(this.form.id);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();
                return;
            }
            this.presensi = resp.data;
            if (this.presensi.ttltelat === null) {
                this.presensi.ttltelat = 0;
            }
            if (this.presensi.ttlkerja === null) {
                this.presensi.ttlkerja = 0;
            }
            this.hide();
        },
        async onSubmit() {
            this.show();
            const [err] = await jadwalService.updatePresensi(
                this.presensi,
                this.presensi.id
            );
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
                msg: "OK",
            });
            this.hide();
            this.onShowPresensi();
        },
        showBukti(params) {
            const viewer = this.$el.querySelector(params).$viewer;
            viewer.show();
        },
        convertMinutes(val) {
            const { hours, remainingMinutes } = convertMinutesToHours(val);

            return `${hours} J ${remainingMinutes} M`;
        },
        selectStatus(val) {
            this.presensi.status = val;
            if (
                val !== null &&
                ![JADWAL_STATUS.PROGRESS, JADWAL_STATUS.SELESAI].includes(val)
            ) {
                console.log(val);
                Object.assign({}, this.presensi, {
                    masuk: "",
                    tgl_pulang: "",
                    pulang: "",
                });
            }
        },
    },
};
</script>
