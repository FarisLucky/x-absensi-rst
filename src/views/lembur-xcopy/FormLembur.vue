<template>
    <BModal
        v-model="modal"
        id="lembur"
        hide-footer
        header-class="p-3 bg-info-subtle"
        class="v-modal-custom"
        centered
        scrollable
        title="Lembur"
    >
        <simplebar data-simplebar style="height: calc(100vh - 112px)">
            <form @submit.prevent="onSubmit">
                <div class="mb-1">
                    <label>Jenis Lembur</label>
                    <v-select
                        v-model="form.jenis_lembur"
                        :options="listLembur"
                        :reduce="(item) => item.id"
                        label="nama"
                        placeholder="Pilih Jenis Lembur"
                        @option:selected="onSelectJenis"
                    >
                    </v-select>
                    <small class="text-danger"
                        >Pilih jenis lembur untuk melanjutkan</small
                    >
                </div>
                <BRow v-if="form.jenis_lembur" class="g-2">
                    <BCol sm="4">
                        <label for="">
                            Tanggal
                            <span class="text-danger">*</span>
                        </label>
                        <flat-pickr
                            v-model="form.tanggal"
                            placeholder="Pilih Tanggal"
                            :config="{
                                // minDate: yest,
                                dateFormat: 'd-m-Y',
                            }"
                            class="form-control bg-light border-light"
                        ></flat-pickr>
                    </BCol>
                    <BCol v-if="form.tanggal != ''" sm="4">
                        <label for="">
                            Mulai
                            <span class="text-danger">*</span>
                        </label>
                        <flat-pickr
                            :modelValue="form.mulai"
                            @update:modelValue="onPilihMulai"
                            placeholder="Pilih Waktu"
                            :config="{
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: 'H:i',
                                time_24hr: true,
                            }"
                            class="form-control bg-light border-light"
                        ></flat-pickr>
                    </BCol>
                    <BCol v-if="form.tanggal != ''" sm="4">
                        <label for="">
                            Akhir
                            <span class="text-danger">*</span>
                        </label>
                        <flat-pickr
                            v-model="form.akhir"
                            placeholder="Pilih Waktu"
                            :config="{
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: 'H:i',
                                time_24hr: true,
                            }"
                            :disabled="m_lembur?.ttl_jam > 0"
                            class="form-control bg-light border-light"
                        ></flat-pickr>
                    </BCol>
                    <BCol lg="12">
                        <label class="mb-1">Ket</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="form.ket"
                        />
                    </BCol>
                    <div v-if="form.anggota > 0" class="mb-1">
                        <label>Anggota</label>
                        <v-select
                            multiple
                            v-model="form.anggota_list"
                            :options="listAnggota"
                            label="nama"
                            placeholder="Pilih Anggota"
                        >
                        </v-select>
                        <small class="text-danger">
                            <b>uang lembur</b> akan dibagi sebanyak anggota
                        </small>
                    </div>
                    <BCol lg="12">
                        <label class="mb-1">Upload</label>
                        <FormUpload ref="fileUpload" />
                    </BCol>
                    <BCol v-if="m_lembur?.id_lokasi !== null" lg="12">
                        <small class="mb-1 text-danger"
                            >Jenis lembur diatas membutuhkan GPS absen
                            <b>Masuk dan Selesai</b></small
                        >
                    </BCol>
                    <BCol
                        v-else-if="
                            m_lembur?.id_lokasi === null &&
                            m_lembur?.absen_foto == 'YA'
                        "
                        lg="12"
                    >
                        <small class="mb-1 text-danger"
                            >Jenis lembur diatas membutuhkan Kamera absen
                            <b>Masuk dan Selesai</b></small
                        >
                    </BCol>
                    <BCol lg="12">
                        <BButton
                            type="submit"
                            variant="primary"
                            class="w-100 mb-5"
                            :disabled="progress"
                        >
                            {{ progress ? "Tunggu Dulu" : "Simpan" }}
                        </BButton>
                    </BCol>
                </BRow>
            </form>
        </simplebar>
    </BModal>
</template>
<script>
import { spinnerMethods, toastMethods } from "@/state/helpers";
import useVuelidate from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import FormUpload from "@/components/form-upload.vue";
import flatPickr from "vue-flatpickr-component";
import { lemburService } from "@/services/LemburService";
import { mLemburService } from "@/services/MLemburService";
import dayjs from "dayjs";
import simplebar from "simplebar-vue";
import { mKaryawanService } from "@/services/MKaryawanService";

const initForm = () => ({
    jenis_lembur: "",
    tanggal: "",
    mulai: "",
    akhir: "",
    ket: "",
    anggota: 0,
    anggota_list: [],
});

export default {
    components: {
        FormUpload,
        flatPickr,
        simplebar,
    },
    data() {
        const user = this.$store.state.auth.data;
        return {
            modal: false,
            form: initForm(),
            progress: false,
            user,
            listLembur: [],
            listAnggota: [],
            m_lembur: {},
            yest: dayjs().subtract(1, "day").format("DD-MM-YYYY"),
        };
    },
    setup() {
        return { v$: useVuelidate() };
    },
    validations() {
        return {
            form: {
                tanggal: { required },
                mulai: { required },
                akhir: { required },
            },
        };
    },
    created() {
        this.getYesterday();
        this.getAnggota();
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        showModal() {
            this.getLembur();
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
            this.onReset();
        },
        onReset() {
            Object.assign(this.$data.form, initForm());
        },
        async getLembur() {
            this.show();
            const [err, resp] = await mLemburService.data(1);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();

                return;
            }
            this.listLembur = resp.data.map((item) => {
                let anggota =
                    item.anggota !== null || item.anggota > 0
                        ? `+ ${item.anggota} Anggota (optional)`
                        : "";
                item.nama = `${item.nama} ${anggota}`;
                return item;
            });
            this.hide();
        },
        async getAnggota() {
            const [err, resp] = await mKaryawanService.karyawanByUnit(
                this.$store.state.auth.data?.jabatans[0]?.m_jabatan.id_unit
            );
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });

                return;
            }
            this.listAnggota = resp.data
                .filter(
                    (anggota) => anggota.nip !== this.$store.state.auth.data.nip
                )
                .map((anggota) => ({ nip: anggota.nip, nama: anggota.nama }));
        },
        onSelectJenis(val) {
            // Reset Form
            Object.assign(this.form, {
                jenis_lembur: val.id,
                tanggal: "",
                mulai: "",
                akhir: "",
                ket: "",
                anggota: val.anggota,
                anggota_list: [],
            });
            this.m_lembur = val;
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
            this.show();

            let formData = new FormData();
            formData.append("jenis_lembur", this.form.jenis_lembur);
            formData.append("tanggal", this.form.tanggal);
            formData.append("mulai", this.form.mulai);
            formData.append("akhir", this.form.akhir);
            formData.append("ket", this.form.ket);
            formData.append(
                "anggota_list",
                JSON.stringify(this.form.anggota_list)
            );
            if (this.$refs.fileUpload?.myFiles[0] !== undefined) {
                formData.append("file", this.$refs.fileUpload?.myFiles[0]);
            }

            const [err] = await lemburService.store(formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();

                return;
            }
            this.hide();
            this.hideModal();
            this.toastSuccess({
                title: "Berhasil",
                msg: "OK",
            });
            this.onReset();
            this.$emit("fetch");
        },
        onPilihMulai(val) {
            let akhir = val;
            if (this.m_lembur?.ttl_jam > 0) {
                akhir = dayjs(`${this.form.tanggal} ${val}`)
                    .add(this.m_lembur.ttl_jam, "hour")
                    .format("HH:mm");
            }
            this.form.mulai = val;
            this.form.akhir = akhir;
        },
        getYesterday() {
            this.yest = dayjs().subtract(1, "day").format("DD-MM-YYYY");
        },
    },
};
</script>
