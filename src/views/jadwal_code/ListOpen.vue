<template>
    <BCard no-body>
        <BCardHeader class="pb-1 text-info">
            <h5 class="fs-14">
                Jadwal
                <strong>Kode Darurat RS</strong>
            </h5>
        </BCardHeader>
        <BCardBody>
            <div class="mb-1">
                <BRow class="g-2 mb-1 align-items-end">
                    <BCol :lg="3">
                        <label class="mb-1">Filter</label>
                        <v-select
                            v-model="filter.shift"
                            :options="['PAGI', 'SIANG', 'MALAM']"
                            placeholder="Pilih Shift"
                            @option:selected="onSelectedShift"
                        ></v-select>
                    </BCol>
                    <BCol>
                        <BButton
                            type="button"
                            variant="outline-secondary"
                            @click="onRefresh"
                        >
                            <i class="ri-refresh-fill me-1 align-bottom"></i>
                            Reset
                        </BButton>
                    </BCol>
                </BRow>
                <BRow class="g-2 align-items-end">
                    <BCol :lg="12">
                        <p class="m-0 mb-1">
                            <b class="fs-18 text-info"
                                ><i>CodeBlue</i>: {{ filter.tanggal }}</b
                            >
                        </p>
                        <div class="mb-1">
                            <vue-good-table
                                mode="local"
                                :columns="columns"
                                :rows="rows"
                                :search-options="{
                                    enabled: true,
                                    externalQuery: filter.search,
                                }"
                                :sort-options="{
                                    enabled: false,
                                }"
                                :isLoading="isLoading"
                                :line-numbers="true"
                                theme="polar-bear"
                                styleClass="vgt-table striped sticky cs-h-20 bgc-info"
                            >
                                <template #table-row="props">
                                    <span v-if="props.column.label == 'Leader'">
                                        <img
                                            src="@/assets/images/blue/lead.png"
                                            width="40"
                                            class="d-block"
                                        />
                                        {{
                                            props.row.lead_by?.nama ?? "KOSONG"
                                        }}
                                    </span>
                                    <span
                                        v-if="
                                            props.column.label == 'Ventilator'
                                        "
                                    >
                                        <img
                                            src="@/assets/images/blue/vent.png"
                                            width="40"
                                            class="d-block"
                                        />
                                        {{
                                            props.row.vent_by?.nama ?? "KOSONG"
                                        }}
                                    </span>
                                    <span
                                        v-if="props.column.label == 'Kompresor'"
                                    >
                                        <img
                                            src="@/assets/images/blue/komp.png"
                                            width="40"
                                            class="d-block"
                                        />
                                        {{
                                            props.row.komp_by?.nama ?? "KOSONG"
                                        }}
                                    </span>
                                    <span
                                        v-if="
                                            props.column.label == 'Sirkulator'
                                        "
                                    >
                                        <img
                                            src="@/assets/images/blue/sirk.png"
                                            width="40"
                                            class="d-block"
                                        />
                                        {{
                                            props.row.sirk_by?.nama ?? "KOSONG"
                                        }}
                                    </span>
                                    <span
                                        v-if="props.column.label == 'Security'"
                                    >
                                        <img
                                            src="@/assets/images/blue/sec.png"
                                            width="40"
                                            class="d-block"
                                        />
                                        {{ props.row.sec_by?.nama ?? "KOSONG" }}
                                    </span>
                                </template>
                            </vue-good-table>
                        </div>
                    </BCol>
                    <BCol :lg="12">
                        <p class="m-0 mb-1">
                            <b class="fs-18 text-danger"
                                ><i>CodeRed</i>: {{ filter.tanggal }}</b
                            >
                        </p>
                        <div class="mb-1">
                            <vue-good-table
                                mode="local"
                                :columns="codeRedColumns"
                                :rows="codeRedRows"
                                :search-options="{
                                    enabled: true,
                                    externalQuery: filter.search,
                                }"
                                :sort-options="{
                                    enabled: false,
                                }"
                                :isLoading="isLoading2"
                                :line-numbers="true"
                                theme="polar-bear"
                                styleClass="vgt-table striped sticky cs-h-20 bgc-danger"
                            >
                                <template #table-row="props">
                                    <span v-if="props.column.field == 'unit'">
                                        {{ props.row.unit.join(" & ") }}
                                    </span>
                                    <span v-if="props.column.label == 'Api'">
                                        <img
                                            src="@/assets/images/code/api.png"
                                            width="60"
                                            class="d-block"
                                        />
                                        {{ props.row.api_by?.nama }}
                                    </span>
                                    <span
                                        v-if="props.column.label == 'Dokumen'"
                                    >
                                        <img
                                            src="@/assets/images/code/dok.png"
                                            width="60"
                                            class="d-block"
                                        />
                                        {{ props.row.dok_by?.nama }}
                                    </span>
                                    <span v-if="props.column.label == 'Pasien'">
                                        <img
                                            src="@/assets/images/code/pasien.png"
                                            width="60"
                                            class="d-block"
                                        />
                                        {{ props.row.pasien_by?.nama }}
                                    </span>
                                    <span v-if="props.column.label == 'Aset'">
                                        <img
                                            src="@/assets/images/code/aset.png"
                                            width="60"
                                            class="d-block"
                                        />
                                        {{ props.row.aset_by?.nama }}
                                    </span>
                                </template>
                            </vue-good-table>
                        </div>
                    </BCol>
                </BRow>
            </div>
        </BCardBody>
    </BCard>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { toastMethods } from "@/state/helpers";
import { jadwalService } from "@/services/JadwalService";
import queryString from "query-string";

const initFilter = () => {
    return {
        search: "",
        shift: "",
        tanggal: "",
    };
};

export default {
    components: {
        VueGoodTable,
    },
    data() {
        return {
            filter: initFilter(),
            columns: [
                {
                    label: "Tanggal",
                    field: "tanggal_cast",
                },
                {
                    label: "Shift",
                    field: "shift",
                },
                {
                    label: "Leader",
                    field: "lead_by.nama",
                },
                {
                    label: "Ventilator",
                    field: "vent_by.nama",
                },
                {
                    label: "Kompresor",
                    field: "komp_by.nama",
                },
                {
                    label: "Sirkulator",
                    field: "sirk_by.nama",
                },
                {
                    label: "Security",
                    field: "sec_by.nama",
                },
            ],
            codeRedColumns: [
                {
                    label: "Shift",
                    field: "shift",
                },
                {
                    label: "Kode",
                    field: "kode_zona",
                },
                {
                    label: "Zona",
                    field: "jadwal_zona.zona",
                },
                {
                    label: "Api",
                    field: "api_by.nama",
                    tdClass: "bg-danger text-light",
                },
                {
                    label: "Dokumen",
                    field: "dok_by.nama",
                    tdClass: "bg-white",
                },
                {
                    label: "Pasien",
                    field: "pasien_by.nama",
                    tdClass: "bg-warning text-light",
                },
                {
                    label: "Aset",
                    field: "aset_by.nama",
                    tdClass: "bg-info text-white",
                },
            ],
            rows: [],
            codeRedRows: [],
            isLoading: false,
            isLoading2: false,
            listShift: [],
        };
    },
    created() {
        this.onRefresh();
    },
    methods: {
        ...toastMethods,
        tdClassFn(row) {
            let bg = "";

            if (row.status != null && row?.presensi !== null) {
                if (row?.presensi?.status === "TEPAT") {
                    bg += "bg-success text-light";
                } else if (row?.presensi?.status === "TELAT") {
                    bg += "bg-danger text-light";
                }
            } else if (row.libur == 1) {
                bg += "text-white bg-secondary";
            } else if (row.status == 3) {
                bg += "text-white bg-warning";
            } else {
                bg += "bg-light";
            }

            return bg;
        },
        async fetchData() {
            this.isLoading = true;

            let query = queryString.stringify(this.filter);
            const [err, resp] = await jadwalService.showCodeBlueHarianOpen(
                query
            );
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            this.filter.shift = resp.data?.filter?.shift;
            this.filter.tanggal = resp.data?.filter?.tanggal;
            this.rows = resp.data?.data;
            this.isLoading = false;
        },
        async fetchDataCodeRed() {
            this.isLoading2 = true;

            let query = queryString.stringify(this.filter);
            const [err, resp] = await jadwalService.showCodeRedHarianOpen(
                query
            );
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading2 = false;

                return;
            }
            this.codeRedRows = resp.data?.data;
            this.isLoading2 = false;
        },
        onRefresh() {
            this.filter = initFilter();
            this.fetchData();
            this.fetchDataCodeRed();
        },
        onSelectedShift() {
            Promise.all([this.fetchData(), this.fetchDataCodeRed()]);
        },
    },
};
</script>
<style>
.cs-h-20 {
    height: 20vh;
}
</style>
