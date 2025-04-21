<script>
import { required } from "@vuelidate/validators";
import { authService } from "@/services/AuthService";

import { toastMethods, authMethods, spinnerMethods } from "@/state/helpers";
import Notification from "@/components/notification.vue";
import Cookies from "js-cookie";
import useVuelidate from "@vuelidate/core";

export default {
  components: {
    Notification,
  },
  setup() {
    return { v$: useVuelidate() };
  },
  data() {
    return {
      form: {
        nip: "",
        password: "",
        device: "web",
      },
      submitted: false,
      authError: null,
      tryingToLogIn: false,
      isAuthError: false,
      processing: false,
      passVisible: false,
    };
  },
  validations() {
    return {
      form: {
        nip: { required },
        password: { required },
      },
    };
  },
  methods: {
    ...toastMethods,
    ...authMethods,
    ...spinnerMethods,

    // Try to log the user in with the username
    // and password they provided.
    async tryToLogIn() {
      this.processing = true;
      this.submitted = true;
      // stop here if form is invalid
      this.$touch;
      // show spinner
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

      const [err, resp] = await authService.store(this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors?.nip[0],
        });
        this.isLoading = false;
        this.processing = false;

        // hide spinner
        this.hide();
        return;
      }
      this.login(resp.data);
      Cookies.set(
        "cki-absen",
        JSON.stringify({
          data: resp.data.user,
          token: resp.data.token,
        }),
        {
          expires: 90,
        }
      );
      this.processing = false;
      // hide spinner
      this.hide();

      this.$router.go("/");
    },
  },
};
</script>

<template>
  <div class="auth-page-wrapper pt-5">
    <Notification />
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
              <p class="mt-4 fs-15 fw-medium">PT Catur Karsa Inkrisuba</p>
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
                <BRow class="align-items-center">
                  <BCol :sm="4">
                    <div class="rounded border border-light d-inline-block">
                      <router-link
                        to="/"
                        class="d-inline-block auth-logo bg-white p-2 rounded"
                      >
                        <img src="@/assets/images/logo.png" alt="" width="80" />
                      </router-link>
                    </div>
                  </BCol>
                  <BCol>
                    <div class="mt-2">
                      <h5 class="text-primary">
                        Absenku PT Catur Karsa Inkrisuba
                      </h5>
                      <p class="text-muted">
                        Silahkan login untuk melanjutkan.
                      </p>
                    </div>
                  </BCol>
                </BRow>
                <div class="p-2">
                  <form @submit.prevent="tryToLogIn" autocomplete="false">
                    <div class="mb-1">
                      <label for="nip" class="form-label">NIP</label>
                      <input
                        type="number"
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
                      <label class="form-label" for="password-input"
                        >Password</label
                      >
                      <div class="position-relative auth-pass-inputgroup mb-3">
                        <input
                          :type="passVisible ? 'text' : 'password'"
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
                          @click.prevent="() => (passVisible = !passVisible)"
                        >
                          <i class="ri-eye-fill align-middle"></i>
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
                        {{ processing ? "Please wait" : "Sign In" }}
                      </BButton>
                    </div>

                    <div class="mt-4 mb-4 text-center"></div>
                  </form>
                </div>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </div>
  </div>
</template>
