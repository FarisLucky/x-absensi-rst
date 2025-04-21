<template>
    <BCard no-body>
        <!-- Scrollable tabs -->
        <BCardHeader header-tag="nav">
            <div class="mb-2">
                <label>Pilih Bulan</label>
                <MonthPickerInput
                    lang="id"
                    v-model="filter.month"
                    @input="onFilterMonthFn"
                    placeholder="Pilih Bulan"
                    :clearable="true"
                    :default-month="new Date().getMonth() + 1"
                    :default-year="new Date().getFullYear()"
                ></MonthPickerInput>
            </div>
            <BNav card-header small pills>
                <BNavItem
                    v-for="l in daysInMonth"
                    :key="l"
                    :active="activeTab === l.day"
                    @click.prevent="getPresensi(l.date)"
                >
                    <h5>{{ l?.day }}</h5>
                    <span>{{ l?.dayName }}</span>
                </BNavItem>
            </BNav>
        </BCardHeader>
        <BCardBody>
            <BRow class="justify-content-center">
                <BCol cols="12" md="6">
                    <BListGroup v-if="lists.length > 0">
                        <BListGroupItem
                            v-for="(list, idx) in lists"
                            :key="idx"
                            class="d-flex justify-content-between align-items-center py-3"
                        >
                            <div class="mt-2 mb-3">
                                <div class="py-1">
                                    <i
                                        class="ri-calendar-2-line me-2 fs-13"
                                    ></i>
                                    <span class="fs-12"> Shift: </span>
                                    <span class="fs-12 text-muted">
                                        {{
                                            list?.libur ? "Libur" : list?.shift
                                        }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <i class="ri-calendar-line me-2 fs-13"></i>
                                    <span class="fs-12"> Tanggal: </span>
                                    <span class="fs-12 text-muted">
                                        {{ list?.tanggal_cast }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <i class="ri-time-line me-2 fs-13"></i>
                                    <span> Masuk: </span>
                                    <span class="fs-12 text-muted">
                                        {{
                                            `${list.masuk ?? ""} - ${
                                                list.pulang ?? ""
                                            }`
                                        }}
                                    </span>
                                    <span
                                        v-if="list?.ttlkerja !== null"
                                        class="fs-12 text-muted"
                                    >
                                        {{ ` (${list.ttlkerja ?? "-"} jam)` }}
                                    </span>
                                </div>
                                <div v-if="list?.status !== null" class="py-1">
                                    <i
                                        class="ri-user-location-line me-2 fs-13"
                                    ></i>
                                    <span> Lokasi: </span>
                                    <span class="fs-12 text-muted">
                                        {{ list.lokasi }}
                                    </span>
                                </div>
                                <div v-if="list?.status !== null" class="py-1">
                                    <i
                                        class="ri-file-list-2-line me-2 fs-13"
                                    ></i>
                                    <span
                                        v-if="
                                            list?.status === JADWAL_STATUS.IZIN
                                        "
                                        class="mb-0 fs-12"
                                    >
                                        Alasan:
                                        <span class="text-muted">{{
                                            list?.izin?.ket
                                        }}</span>
                                    </span>
                                    <span
                                        v-if="
                                            list?.status ===
                                                JADWAL_STATUS.PROGRESS ||
                                            list?.status ===
                                                JADWAL_STATUS.SELESAI
                                        "
                                        class="mb-0 fs-12"
                                    >
                                        Alasan Telat:
                                        <span class="text-muted">{{
                                            list?.presensi_terlambat?.ket
                                        }}</span>
                                    </span>
                                </div>
                            </div>
                            <div
                                v-if="list.status !== null"
                                class="aler"
                                :class="{
                                    'alert-success':
                                        list?.status ===
                                            JADWAL_STATUS.PROGRESS ||
                                        list?.status === JADWAL_STATUS.SELESAI,
                                    'alert-warning':
                                        list.status === JADWAL_STATUS.IZIN,
                                    'alert-danger':
                                        list.status === JADWAL_STATUS.IZIN,
                                }"
                            >
                                <span
                                    v-if="
                                        list.status ===
                                        JADWAL_STATUS.TIDAK_HADIR
                                    "
                                >
                                    {{ list.izin?.izin }}
                                </span>
                                <span v-else>
                                    {{ list.status_absen }}
                                </span>
                            </div>
                            <div v-else class="alert alert-secondary">
                                BELUM ABSEN
                            </div>
                        </BListGroupItem>
                    </BListGroup>
                    <h4 v-else class="py-3 text-center">Tidak Ada Jadwal</h4>
                </BCol>
            </BRow>
        </BCardBody>
    </BCard>
</template>
<script>
import { jadwalService } from "@/services/JadwalService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { JADWAL_STATUS } from "@/helpers/utils";
import { MonthPickerInput } from "vue-month-picker";

export default {
    components: {
        MonthPickerInput,
    },
    data() {
        let date = new Date();
        return {
            lists: [],
            activeTab: date.getDate(),
            now: date,
            days: [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu",
            ],
            startDate: new Date(date.getFullYear(), date.getMonth() + 1, 1),
            daysInMonth: [],
            JADWAL_STATUS,
            filter: {
                month: "",
            },
            // const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
        };
    },
    created() {
        this.generateMonthDays();
        this.getPresensi(this.now);
    },
    watch: {
        "filter.month"(newValue) {
            console.log(newValue);
            // this.filter.month = new Date(date.getFullYear(), date.getMonth() + 1, 1)
        },
    },
    methods: {
        ...spinnerMethods,
        ...toastMethods,
        generateMonthDays() {
            const endDate = new Date(this.startDate);
            endDate.setMonth(endDate.getMonth() + 1);

            const days = [];
            for (
                let d = new Date(this.startDate);
                d < endDate;
                d.setDate(d.getDate() + 1)
            ) {
                days.push({
                    date: new Date(d.getFullYear(), d.getMonth(), d.getDate()),
                    day: d.getDate(),
                    dayName: this.days[d.getDay()],
                    isWeekend: [0, 6].includes(d.getDay()),
                });
            }

            this.daysInMonth = days;
        },
        async getPresensi(tgl) {
            this.show();
            console.log(tgl);
            let date = `${tgl.getFullYear()}-${
                tgl.getMonth() + 1
            }-${tgl.getDate()}`;
            const [err, resp] = await jadwalService.showByDate(date);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();
                return;
            }
            this.lists = resp.data;
            this.activeTab = tgl.getDate();
            this.hide();
        },
        onFilterMonthFn(newVal) {
            this.startDate = new Date(newVal.year, newVal.monthIndex, 1);
            // console.log(newVal);
            this.generateMonthDays();
            this.getPresensi(newVal.from);
            // this.filter.month = new Date(date.getFullYear(), date.getMonth() + 1, 1)
        },
    },
};
</script>

<style scoped>
.nav {
    overflow-x: auto;
    overflow-y: hidden;
    flex-wrap: nowrap;
}
.nav-item {
    white-space: nowrap;
}
</style>
