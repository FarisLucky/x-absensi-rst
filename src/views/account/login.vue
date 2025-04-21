<template>
    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    version="1.1"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120"
                >
                    <path
                        d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"
                    ></path>
                </svg>
            </div>
        </div>

        <div
            class="auth-page-content"
            :style="{ background: '@/assets/images/web.jpg' }"
        >
            <BContainer>
                <BRow>
                    <BCol lg="12">
                        <div class="text-center mt-sm-5 mb-5 text-white-50">
                            <p class="mt-4 fs-15 fw-medium">
                                PT Catur Karsa Inkrisuba
                            </p>
                        </div>
                    </BCol>
                </BRow>
                <BRow class="justify-content-center">
                    <BCol md="8" lg="6" xl="5">
                        <BCard
                            no-body
                            class="mt-4"
                            style="background-color: rgba(255, 255, 255, 0.9)"
                        >
                            <BCardBody class="p-4">
                                <BRow
                                    class="align-items-center justify-content-center g-2"
                                >
                                    <BCol cols="12">
                                        <div class="mb-0">
                                            <span>
                                                Device ID:
                                                <b class="text-primary">
                                                    {{ deviceId }}
                                                </b>
                                            </span>
                                        </div>
                                        <small
                                            v-if="deviceId === null"
                                            class="text-warning"
                                            >Jika <b>DEVICE ID</b> kosong, maka
                                            klik tombol refresh dulu</small
                                        >
                                    </BCol>
                                    <BCol cols="4">
                                        <div
                                            class="rounded border border-light d-inline-block"
                                        >
                                            <router-link
                                                to="/"
                                                class="d-inline-block auth-logo bg-white p-2 rounded"
                                            >
                                                <img
                                                    src="@/assets/images/logo_new.png"
                                                    alt=""
                                                    width="80"
                                                />
                                            </router-link>
                                        </div>
                                    </BCol>
                                    <BCol>
                                        <div class="mt-2 w-75 text-center">
                                            <h5 class="text-primary fs-16">
                                                PT Catur Karsa Inkrisuba
                                            </h5>
                                            <h4 class="fs-22">Absensi</h4>
                                            <p class="text-muted">
                                                Silahkan login untuk
                                                melanjutkan.
                                            </p>
                                        </div>
                                    </BCol>
                                </BRow>
                                <div class="p-2">
                                    <form
                                        @submit.prevent="tryToLogIn"
                                        autocomplete="off"
                                    >
                                        <div class="mb-1">
                                            <label for="nip" class="form-label"
                                                >NIP</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="nip"
                                                placeholder="Enter NIP"
                                                v-model="form.nip"
                                                autocomplete="false"
                                                required
                                            />
                                            <div class="invalid-feedback">
                                                <span></span>
                                            </div>
                                        </div>

                                        <div class="mb-1">
                                            <label
                                                class="form-label"
                                                for="password-input"
                                                >Password</label
                                            >
                                            <div
                                                class="position-relative auth-pass-inputgroup mb-3"
                                            >
                                                <input
                                                    :type="
                                                        passVisible
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    v-model="form.password"
                                                    class="form-control pe-5"
                                                    placeholder="Enter password"
                                                    id="password-input"
                                                    autocomplete="false"
                                                    required
                                                />
                                                <BButton
                                                    variant="link"
                                                    class="position-absolute end-0 top-0 text-decoration-none text-muted"
                                                    type="button"
                                                    id="password-addon"
                                                    @click.prevent="
                                                        () =>
                                                            (passVisible =
                                                                !passVisible)
                                                    "
                                                >
                                                    <i
                                                        class="ri-eye-fill align-middle"
                                                    ></i>
                                                </BButton>
                                                <div class="invalid-feedback">
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-1">
                                            <BButton
                                                variant="success"
                                                class="w-100"
                                                type="submit"
                                                :disabled="processing"
                                            >
                                                {{
                                                    processing
                                                        ? "Please wait"
                                                        : "Sign In"
                                                }}
                                            </BButton>
                                        </div>
                                        <div class="mt-1">
                                            <span class="fs-12">
                                                ini perangkat anda ? ajukan
                                            </span>
                                            <BLink
                                                class="text-danger fs-14"
                                                @click.prevent="showPengajuan"
                                            >
                                                disini
                                            </BLink>
                                        </div>

                                        <div
                                            class="mt-4 mb-4 text-center"
                                        ></div>
                                    </form>
                                </div>
                            </BCardBody>
                        </BCard>
                    </BCol>
                </BRow>
                <BModal
                    v-model="modal"
                    id="izin"
                    hide-footer
                    header-class="p-3 bg-info-subtle"
                    class="v-modal-custom"
                    size="md"
                    centered
                    title="Ajukan Perangkat"
                >
                    <form @submit.prevent="onSubmitAjukan">
                        <BRow class="g-2 align-items-end">
                            <BCol md="6">
                                <label>NIP</label>
                                <input
                                    class="form-control"
                                    v-model="modalForm.nip"
                                />
                            </BCol>
                            <BCol md="6">
                                <label>Alasan</label>
                                <input
                                    class="form-control"
                                    v-model="modalForm.ket"
                                />
                            </BCol>
                            <BCol>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </BCol>
                        </BRow>
                    </form>
                </BModal>
            </BContainer>
        </div>
    </div>
</template>
<script>
import { required } from "@vuelidate/validators";
import { authService } from "@/services/AuthService";
import { toastMethods, authMethods, spinnerMethods } from "@/state/helpers";
import Cookies from "js-cookie";
import useVuelidate from "@vuelidate/core";
import { Device } from "@capacitor/device";
import { encryptData } from "@/helpers/encryption";
import { generateDeviceFingerprint } from "@/helpers/deviceFingerprint";
import { Preferences } from "@capacitor/preferences";

export default {
    setup() {
        return { v$: useVuelidate() };
    },
    validations() {
        return {
            form: {
                nip: { required },
                password: { required },
            },
        };
    },
    data() {
        return {
            form: {
                nip: "",
                password: "",
                device: "web",
                deviceId: "",
                deviceType: "",
                deviceOs: "",
                detail: "",
            },
            processing: false,
            passVisible: false,
            deviceInfo: null,
            deviceId: null,
            modal: false,
            modalForm: {
                nip: "",
                ket: "",
            },
        };
    },
    watch: {
        "form.nip"(newValue) {
            this.form.nip = newValue.toString().toUpperCase();
        },
    },
    async created() {
        await this.getDeviceInfoCapacitor();
        const currentDeviceId = await generateDeviceFingerprint();
        this.deviceId = currentDeviceId.identifier;
        this.deviceCompId = currentDeviceId.compositeId;
    },
    methods: {
        ...toastMethods,
        ...authMethods,
        ...spinnerMethods,

        // Try to log the user in with the username
        // and password they provided.
        async tryToLogIn() {
            this.processing = true;
            this.show();

            const result = await this.v$.$validate();
            if (!result) {
                this.toastError({
                    title: "Gagal",
                    msg: "Form wajib diisi",
                });
                this.hide();
                return;
            }

            let header = {
                headers: {
                    "X-Device-ID": this.deviceCompId,
                    "X-Device-PLATFORM": this.deviceInfo.platform,
                    "X-Device-OS": this.deviceInfo.operatingSystem,
                },
            };

            const [err, resp] = await authService.loginOne(this.form, header);

            if (err) {
                if (err.response?.data?.errors?.session?.length > 0) {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors?.session[0],
                    });
                } else {
                    this.toastError({
                        title: "Gagal",
                        msg: err.response?.data?.errors?.nip[0],
                    });
                }
                this.processing = false;

                this.hide();
                return;
            }

            await Preferences.configure({ group: "AUTH_SECURE" });

            await Preferences.set({
                key: "user_device_pair",
                value: JSON.stringify({
                    userId: resp.data.user.id,
                    deviceFingerprint: this.deviceCompId,
                    lastVerified: new Date().toISOString(),
                }),
            });

            // set state
            this.login(resp.data);

            let payload = encryptData(
                JSON.stringify({
                    data: resp.data.user,
                    token: resp.data.token,
                })
            );

            // set login session
            Cookies.set("cki-absen", payload, {
                expires: 90,
            });

            this.processing = false;
            // hide spinner
            this.hide();

            this.$router.go("/");
        },

        async getDeviceInfoCapacitor() {
            const info = await Device.getInfo();

            this.deviceInfo = info;
            this.form.detail = info;
        },

        generateRandomString() {
            return Math.round(Date.now()).toString(36);
        },
        showPengajuan() {
            this.modal = true;
        },
        hidePengajuan() {
            this.modal = false;
        },
        async onSubmitAjukan() {
            this.show();
            this.form.deviceId = this.deviceCompId;
            this.form.deviceType = this.deviceInfo.platform;
            this.form.deviceOs = this.deviceInfo.operatingSystem;
            const [err] = await authService.pengajuan(this.modalForm);

            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data,
                });

                this.hide();
                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "Silahkan menginfokan ke Manajemen agar diapproval",
            });
            this.hide();
            this.modalForm.nip = "";
            this.hidePengajuan();
        },
        reloadPage() {
            window.location.reload();
        },
    },
};
</script>
