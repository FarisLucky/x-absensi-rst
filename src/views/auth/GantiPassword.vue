<style scoped>
.modal {
    z-index: 9999;
}
img {
    width: 100%;
    height: 100%;
}
</style>
<template>
    <BModal
        v-model="modal"
        id="showmodal"
        modal-class="zoomIn"
        hide-footer
        header-class="p-3 bg-info-subtle"
        class="v-modal-custom"
        centered
        size="md"
        title="Import Jadwal"
    >
        <div class="mb-1">
            <p>
                Setelah ganti password, Anda akan
                <i class="text-danger">logout</i> otomatis
            </p>
        </div>
        <div class="mb-1">
            <label for="password">Password</label>
            <div class="input-group">
                <input
                    :type="viewPassword"
                    v-model="form.password"
                    class="form-control"
                />
                <button
                    class="btn btn-secondary"
                    type="button"
                    id="button-addon2"
                    @click="changeView"
                >
                    {{ viewPassword === "text" ? "Hide" : "Show" }}
                </button>
            </div>
        </div>
        <div class="mb-1">
            <label for="password">Konfirmasi Password</label>
            <div class="input-group">
                <input
                    :type="viewPasswordConfirm"
                    v-model="form.password_confirm"
                    class="form-control"
                />
                <button
                    class="btn btn-secondary"
                    type="button"
                    id="button-addon2"
                    @click="changeViewConfirm"
                >
                    {{ viewPasswordConfirm === "text" ? "Hide" : "Show" }}
                </button>
            </div>
        </div>
        <span v-if="checkPass" class="text-danger">Password tidak sama</span>
        <div class="mb-1">
            <button
                type="button"
                class="btn btn-primary d-block"
                @click.prevent="onSubmit"
                :disabled="checkPass"
            >
                Simpan
            </button>
        </div>
    </BModal>
</template>
<script>
import Cookies from "js-cookie";
import { toastMethods } from "@/state/helpers";
import { authService } from "@/services/AuthService";

export default {
    data() {
        return {
            form: {
                password: "",
                password_confirm: "",
            },
            viewPassword: "password",
            viewPasswordConfirm: "password",
            modal: null,
            checkPass: false,
        };
    },
    watch: {
        "form.password_confirm"() {
            if (
                this.form.password !== "" &&
                this.form.password_confirm !== this.form.password
            ) {
                this.checkPass = true;
            } else {
                this.checkPass = false;
            }
        },
        "form.password"() {
            if (
                this.form.password !== "" &&
                this.form.password_confirm !== this.form.password
            ) {
                this.checkPass = true;
            } else {
                this.checkPass = false;
            }
        },
    },
    methods: {
        ...toastMethods,
        showModal() {
            this.modal = true;
        },
        hideModal() {
            this.modal = false;
        },
        changeView() {
            if (this.viewPassword === "password") {
                this.viewPassword = "text";
            } else {
                this.viewPassword = "password";
            }
        },
        changeViewConfirm() {
            if (this.viewPasswordConfirm === "password") {
                this.viewPasswordConfirm = "text";
            } else {
                this.viewPasswordConfirm = "password";
            }
        },

        async onSubmit() {
            if (!this.checkPass && this.form.password !== "") {
                const [err] = await authService.changePassword({
                    password: this.form.password,
                });
                if (err) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors,
                    });

                    return;
                }
                Cookies.remove("cki-absen");
                this.$router.go();
            }
        },
    },
};
</script>