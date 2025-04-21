<template>
    <BRow class="g-2 justify-content-center">
        <BCol md="8">
            <BCard no-body>
                <BCardHeader>
                    <h4 class="mb-1">Pengajuan Lembur</h4>
                    <span>
                        Harap isi formulir berikut untuk mengajukan lembur
                    </span>
                </BCardHeader>
                <BCardBody>
                    <form @submit.prevent="onSubmit" autocomplete="off">
                        <ul style="list-style-type: none">
                            <li>
                                <label class="mb-1">Tanggal Lembur</label>
                                <flat-pickr
                                    v-model="form.tanggal"
                                    placeholder="Pilih Tanggal"
                                    :config="{
                                        altInput: true,
                                        wrap: true, // set wrap to true only when using 'input-group'
                                        altFormat: 'd-m-Y',
                                        dateFormat: 'Y-m-d',
                                    }"
                                    class="form-control bg-light border-light"
                                    required
                                ></flat-pickr>
                                <div class="mb-2 mt-1">
                                    <small
                                        >Pilih tanggal untuk lembur yang
                                        diajukan</small
                                    >
                                </div>
                            </li>
                            <li>
                                <BRow>
                                    <BCol cols="6">
                                        <label class="mb-1">Jam Mulai</label>
                                        <flat-pickr
                                            v-model="form.mulai"
                                            placeholder="Pilih Tanggal"
                                            :config="{
                                                altInput: true,
                                                altFormat: 'd-m-Y H:i',
                                                wrap: true, // set wrap to true only when using 'input-group'
                                                enableTime: true,
                                                dateFormat: 'Y-m-d H:i',
                                                time_24hr: true,
                                            }"
                                            class="form-control bg-light border-light"
                                            required
                                        ></flat-pickr>
                                    </BCol>
                                    <BCol cols="6">
                                        <label class="mb-1">Jam Akhir</label>
                                        <flat-pickr
                                            v-model="form.akhir"
                                            placeholder="Pilih Tanggal"
                                            :config="{
                                                altInput: true,
                                                altFormat: 'd-m-Y H:i',
                                                wrap: true, // set wrap to true only when using 'input-group'
                                                enableTime: true,
                                                dateFormat: 'Y-m-d H:i',
                                                time_24hr: true,
                                            }"
                                            class="form-control bg-light border-light"
                                            required
                                        ></flat-pickr>
                                    </BCol>
                                </BRow>
                            </li>
                            <li>
                                <label class="mb-1">Catatan</label>
                                <textarea
                                    class="form-control"
                                    v-model="form.catatan"
                                    rows="3"
                                    placeholder="Deskripsi alasan pengajuan lembur"
                                ></textarea>
                                <div class="mt-1 mb-2">
                                    <small
                                        >Berikan penjelasan detail mengenai
                                        kebutuhan lembur</small
                                    >
                                </div>
                            </li>
                            <li>
                                <BRow class="mt-3">
                                    <BCol cols="4">
                                        <BButton
                                            type="button"
                                            variant="outline-secondary"
                                            class="w-100"
                                            >Batal</BButton
                                        >
                                    </BCol>
                                    <BCol cols="8">
                                        <BButton
                                            type="submit"
                                            variant="primary"
                                            class="w-100"
                                            >Ajukan Lembur</BButton
                                        >
                                    </BCol>
                                </BRow>
                            </li>
                        </ul>
                    </form>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
</template>
<script>
import { historyLemburService } from "@/services/HistoryLemburService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";

const initForm = () => ({
    nip: "",
    tanggal: "",
    mulai: "",
    akhir: "",
    catatan: "",
});

export default {
    components: {
        flatPickr,
    },
    data() {
        return {
            modal: false,
            form: initForm(),
        };
    },
    methods: {
        ...spinnerMethods,
        ...toastMethods,
        async onSubmit() {
            this.show();
            const [err] = await historyLemburService.store(this.form);
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
            this.$router.push({
                name: "ListLembur",
            });
        },
    },
};
</script>
