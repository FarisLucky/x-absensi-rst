<template>
    <BRow class="g-1">
        <BCol lg="12">
            <h5 class="fs-13">Keterangan</h5>
            <div class="mb-1">
                <BRow>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Pengajuan:</p>
                            <div class="mb-1">
                                <strong>
                                    {{ lembur?.created_at }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Tanggal:</p>
                            <div class="mb-1">
                                <strong>
                                    {{ lembur?.tanggal_cast }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Lembur:</p>
                            <div class="mb-1">
                                <strong>
                                    {{ lembur?.lembur }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Waktu:</p>
                            <div class="mb-1">
                                <strong>
                                    {{ `${lembur?.mulai} - ${lembur?.akhir}` }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Total:</p>
                            <div class="mb-1">
                                <strong>
                                    {{ lembur?.ttl_jam_cast }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <div class="mb-1">
                            <p class="m-0 mb-1">Absen:</p>
                            <div
                                class="mb-1 bg-danger text-white rounded px-1 d-inline-block"
                            >
                                <strong>
                                    {{ lembur?.absen }}
                                </strong>
                            </div>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3">
                        <p class="m-0 mb-1">Keterangan:</p>
                        <div class="mb-1">
                            <strong v-if="lembur?.ket !== null">{{
                                lembur?.ket
                            }}</strong>
                            <strong v-else>-</strong>
                        </div>
                    </BCol>
                    <BCol cols="6" md="3" v-if="lembur?.bukti !== null">
                        <p class="m-0 mb-1">Bukti:</p>
                        <div v-if="lembur?.bukti" class="mb-1">
                            <button
                                class="btn btn-soft-success waves-effect waves-light"
                                @click.prevent="show"
                            >
                                <i class="ri-image-2-fill"></i>
                                Lihat
                            </button>
                            <div
                                class="images d-none"
                                v-viewer="{ movable: false }"
                            >
                                <img
                                    v-for="src in [
                                        lembur.bukti_pengajuan_url_cast,
                                    ]"
                                    :key="src"
                                    :src="src"
                                />
                            </div>
                        </div>
                        <span v-else>-</span>
                    </BCol>
                    <BCol
                        v-if="lembur.list_anggota?.length > 0"
                        cols="12"
                        md="3"
                    >
                        <div class="mb-1">
                            <p class="m-0 mb-1">Anggota:</p>
                            <ul class="ps-3">
                                <li
                                    v-for="(
                                        anggota, idx
                                    ) in lembur.list_anggota"
                                    :key="idx"
                                >
                                    <span>{{ anggota?.nama }}</span>
                                </li>
                            </ul>
                        </div>
                    </BCol>
                </BRow>
            </div>
        </BCol>
        <BCol :lg="12">
            <h5 class="fs-13">Menunggu Persetujuan:</h5>
            <timeline :lembur="lembur" />
        </BCol>
        <BModal
            v-model="modalJadwal"
            hide-footer
            class="v-modal-custom"
            centered
            hide-header-close
            size="xl"
            header-class="p-3 bg-primary-subtle"
            title="Jadwal Karyawan"
        >
            <div class="modal-body text-center p-2">
                <div v-if="jadwal.length > 0" class="table-responsive mb-1">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Unit</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th class="bg-danger-info">Absen</th>
                                <th class="bg-danger-info">Pulang</th>
                                <th>Ket</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(j, idx) in jadwal" :key="idx">
                                <td>{{ ++idx }}</td>
                                <td>{{ j?.m_unit.nama }}</td>
                                <td>{{ j?.nama }}</td>
                                <td>{{ j?.tanggal_cast }}</td>
                                <td>
                                    {{
                                        `${j?.kode_shift} (${
                                            j?.jam_masuk ?? ""
                                        } - ${j?.jam_pulang ?? ""})`
                                    }}
                                </td>
                                <td>{{ j?.presensi?.masuk }}</td>
                                <td>
                                    {{
                                        `${
                                            j?.presensi?.tgl_pulang_cast ?? ""
                                        } ${j?.presensi?.pulang ?? ""}`
                                    }}
                                </td>
                                <td>
                                    {{ j?.presensi?.presensi_terlambat?.ket }}
                                </td>
                                <td
                                    v-if="
                                        j?.status == null &&
                                        j?.kode_shift != null
                                    "
                                >
                                    BELUM ABSEN
                                </td>
                                <td
                                    v-if="
                                        j?.status == null &&
                                        j?.kode_shift == null
                                    "
                                >
                                    LIBUR
                                </td>
                                <td
                                    v-else-if="j?.status == JADWAL_STATUS.SELESAI"
                                >
                                    {{ j?.presensi?.status }}
                                </td>
                                <td
                                    v-else-if="
                                        j?.status == JADWAL_STATUS.PROGRESS
                                    "
                                >
                                    PROGRESS
                                </td>
                                <td
                                    v-else-if="
                                        j?.status == JADWAL_STATUS.TIDAK_HADIR
                                    "
                                >
                                    TIDAK_HADIR
                                </td>
                                <td v-else-if="j?.status == JADWAL_STATUS.IZIN">
                                    IZIN
                                </td>
                                <td
                                    v-else-if="
                                        j?.status == JADWAL_STATUS.TUKAR_OFF
                                    "
                                >
                                    TUKAR_OFF
                                </td>
                                <td v-else-if="j?.status == JADWAL_STATUS.SPPD">
                                    SPPD
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="py-3 border rounded">
                    <h5 class="fs-18">Jadwal Kosong</h5>
                </div>
                <div class="text-end">
                    <BButton
                        variant="secondary"
                        @click.prevent="hideModalJadwal"
                    >
                        <i class="ri-close-line"></i>
                        Tutup
                    </BButton>
                </div>
            </div>
        </BModal>
    </BRow>
</template>
<script>
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { webUrl } from "@/config/http";
import Timeline from "./Timeline";
import { jadwalService } from "@/services/JadwalService";
import queryString from "query-string";
import { toastMethods } from "@/state/helpers";
import { JADWAL_STATUS } from "@/helpers/utils";

export default {
    components: {
        Timeline,
    },
    props: ["lembur", "id"],
    data() {
        return { bukti: [], modalJadwal: false, jadwal: [], JADWAL_STATUS };
    },
    directives: {
        viewer: viewer({
            debug: false,
        }),
    },
    created() {
        this.getBukti(this.id);
    },
    methods: {
        ...toastMethods,
        getBukti(id) {
            let file = `${webUrl}/izin/bukti/${id}`;

            this.bukti.push(file);
        },
        show() {
            const viewer = this.$el.querySelector(".images").$viewer;
            viewer.show();
        },
        async showModalAnggota(params) {
            let query = queryString.stringify(params, {
                arrayFormat: "index",
            });
            const [err, resp] = await jadwalService.show(query);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            this.jadwal = resp.data;
            this.modalJadwal = true;
        },
        hideModalJadwal() {
            this.modalJadwal = false;
            this.jadwal = null;
        },
    },
};
</script>
<style scoped>
.list-group-item {
    border: none;
}
</style>
