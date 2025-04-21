<template>
  <div>
    <div class="mb-1">
      <h5 class="mb-0 fs-18">Konfirmasi Izin</h5>
    </div>
    <div class="d-flex justify-content-between mb-2">
      <form autocomplete="off">
        <div class="search-box">
          <input
            type="text"
            class="form-control search bg-light border-light"
            placeholder="Cari Karyawan..."
            v-model="filter.search"
          />
          <i class="ri-search-line search-icon"></i>
        </div>
      </form>
      <BButton
        variant="soft-secondary"
        @click.prevent="
          () => {
            getConfirmIzin();
            filter.search = '';
          }
        "
      >
        <i class="ri-refresh-fill"></i>
      </BButton>
    </div>
    <div v-if="confirmIzinList.length < 1" class="h-100 border rounded py-4">
      <h3 class="text-center">Belum Ada Permintaan Konfirmasi</h3>
    </div>
    <div v-else>
      <simplebar id="scrollbar" style="height: 60vh; overflow-y: auto">
        <div
          class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-primary"
          id="accordionConfirm"
        >
          <div
            v-for="(izin, idx) in confirmIzinList"
            :key="idx"
            class="accordion-item"
          >
            <h2 class="accordion-header" :id="`${idx}_accordion_btn`">
              <button
                type="button"
                class="accordion-button collapsed fw-semibold"
                data-bs-toggle="collapse"
                :data-bs-target="`#accor_${idx}_body`"
                aria-expanded="false"
                :aria-controls="`accor_${idx}_body`"
              >
                <div class="d-flex align-items-center gap-2">
                  <strong class="fs-13 me-1">
                    <i class="ri-refresh-line text-warning"></i>
                  </strong>
                  <div class="d-inline-block">
                    <strong>
                      {{
                        `${izin?.day_cast}, ${izin?.mulai_cast} - ${izin.izin}`
                      }}
                      :
                      <span class="p-1 bg-primary-subtle rounded">{{
                        `${izin.nip} - ${izin.nama}`
                      }}</span>
                    </strong>
                  </div>
                </div>
              </button>
            </h2>
            <div
              :id="`accor_${idx}_body`"
              class="accordion-collapse collapse"
              :class="{ show: idx < 1 }"
              :aria-labelledby="`${idx}_accordion_btn`"
              data-bs-parent="#accordionConfirm"
            >
              <div class="accordion-body">
                <small class="text-danger"
                  >* Scroll bawah untuk melakukan tindakan</small
                >
                <ProgressIzin
                  :izin="izin"
                  :id="izin.id"
                  class="mb-1"
                  :key="idx"
                />
                <BRow>
                  <BCol v-if="izin?.acc_at === null" :lg="12">
                    <div class="mb-1">
                      <p class="m-0 mb-1">Sebagai Manajemen</p>
                    </div>
                    <div class="mb-1">
                      <BButton
                        variant="success"
                        class="me-1"
                        @click.prevent="
                          onAccSubmit({
                            type: 'acc_manajemen',
                            id_izin: izin.id,
                          })
                        "
                      >
                        <i class="ri-checkbox-circle-fill"></i>
                        Terima
                      </BButton>
                      <BButton
                        variant="soft-danger"
                        @click.prevent="
                          showTolak({
                            jenis: 'manajemen',
                            id_izin: izin.id,
                          })
                        "
                      >
                        <i class="ri-close-circle-fill"></i>
                        Tolak
                      </BButton>
                    </div>
                  </BCol>
                </BRow>
              </div>
            </div>
          </div>
        </div>
      </simplebar>
    </div>
    <form-tolak ref="formTolakRef" @fetch="getConfirmIzin" />
  </div>
</template>
<script>
import FormTolak from "./FormTolak.vue";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { izinService } from "@/services/IzinService";
import { defineAsyncComponent } from "vue";
import simplebar from "simplebar-vue";
import "simplebar-vue/dist/simplebar.min.css";

const initFilter = () => ({
  id_jabatan: "",
  search: "",
});

export default {
  components: {
    FormTolak,
    ProgressIzin: defineAsyncComponent(() => import("./ProgressIzin.vue")),
    simplebar,
  },
  data() {
    const user = this.$store.state?.auth?.data;

    return {
      confirmIzinList: [],
      confirmIzinTemp: [],
      user,
      accNip: 0,
      sdmNip: null,
      filter: initFilter(),
    };
  },
  created() {
    this.getConfirmIzin();
  },
  watch: {
    "filter.search"(newValue) {
      this.confirmIzinList = this.confirmIzinTemp.filter((word) =>
        word?.nama?.toLowerCase().includes(newValue.toLowerCase())
      );
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async getConfirmIzin() {
      this.show();
      const [err, resp] = await izinService.confirmIzin();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.hide();
      this.confirmIzinList = resp.data;
      this.confirmIzinTemp = resp.data;
    },
    async onAccSubmit(params) {
      if (confirm("Apakah ingin diterima ?")) {
        this.show();

        const [err] = await izinService.accSubmit(params);
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          this.hide();

          return;
        }
        this.hide();
        this.getConfirmIzin();
      }
    },
    showTolak(params) {
      this.$refs.formTolakRef.form.id = params.id_izin;
      this.$refs.formTolakRef.form.acc = params.jenis;
      this.$refs.formTolakRef.showModal();
    },
  },
};
</script>
