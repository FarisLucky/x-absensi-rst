<template>
    <BModal
        v-model="modal"
        hide-footer
        :title="title"
        class="v-modal-custom"
        size="sm"
    >
        <div>
            <p class="m-0 mb-2">Setting Password untuk User Karyawan:</p>
            <form method="post" @submit.prevent="onSubmit">
                <div class="mb-1">
                    <label class="form-label">Password</label>
                    <input
                        type="text"
                        v-model="password"
                        class="form-control"
                        autofocus
                    />
                </div>
                <div class="mb-1">
                    <label class="form-label">Super Admin</label>
                    <v-select v-model="role" :options="['YA', 'TIDAK']">
                    </v-select>
                </div>
                <div class="modal-footer v-modal-footer">
                    <BButton
                        type="submit"
                        variant="primary"
                        :disabled="isLoading"
                    >
                        <i class="ri-save-2-fill me-1 align-middle"></i>
                        {{ isLoading ? "Tunggu Dulu" : "Simpan" }}
                    </BButton>
                </div>
            </form>
        </div>
    </BModal>
</template>
<script>
import { mKaryawanService } from "@/services/MKaryawanService";
import { toastMethods } from "@/state/helpers";

const initData = () => ({
    title: "Setting Password",
    modal: false,
    karyawan: [],
    password: "",
    role: "TIDAK",
    isLoading: false,
});

export default {
    data() {
        return initData();
    },
    methods: {
        ...toastMethods,
        showModal() {
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
        },
        async onSubmit() {
            this.isLoading = true;
            const [err, resp] = await mKaryawanService.createUser({
                karyawan: this.karyawan,
                password: this.password,
                role: this.role,
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
            this.toastSuccess({
                title: "Berhasil",
                msg: resp.data,
            });
            Object.assign(this.$data, initData());
            this.$emit("fetch");
            this.hideModal();
        },
    },
};
</script>
