<template>
  <div>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <p class="text-end m-0 mb-1">
        (<strong class="text-danger">*</strong>) tidak boleh kosong
      </p>
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">Data Shift</h5>
        <BRow class="g-2 mb-1">
          <BCol lg="1">
            <label for="kode" class="form-label">
              Kode
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="kode"
              class="form-control"
              placeholder="PG..."
              v-model="form.kode"
            />
          </BCol>
          <BCol lg="3">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="PAGI..."
              v-model="form.nama"
            />
          </BCol>
          <BCol lg="1">
            <label for="mulai_absen" class="form-label">
              Mulai
              <span class="text-danger">*</span>
            </label>
            <input
              type="number"
              id="mulai_absen"
              class="form-control"
              placeholder="2..."
              v-model="form.mulai_absen"
            />
          </BCol>
          <BCol lg="2">
            <label for="jam_masuk" class="form-label">
              Jam Masuk
              <span class="text-danger">*</span>
            </label>
            <flat-pickr
              v-model="form.jam_masuk"
              placeholder="Pilih Jam Masuk"
              :config="{
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
              }"
              class="form-control bg-light border-light"
            ></flat-pickr>
          </BCol>
          <BCol lg="1">
            <label for="telat_masuk" class="form-label">
              Telat
              <span class="text-danger">*</span>
            </label>
            <input
              type="number"
              id="telat_masuk"
              class="form-control"
              placeholder="2..."
              v-model="form.telat_masuk"
            />
          </BCol>
          <BCol lg="2">
            <label for="jam_pulang" class="form-label">
              Jam Pulang
              <span class="text-danger">*</span>
            </label>
            <flat-pickr
              v-model="form.jam_pulang"
              placeholder="Pilih Jam Pulang"
              :config="{
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
              }"
              class="form-control bg-light border-light"
            ></flat-pickr>
          </BCol>
          <BCol lg="2">
            <label for="telat_pulang" class="form-label">
              Telat Pulang
              <span class="text-danger">*</span>
            </label>
            <input
              type="number"
              id="telat_pulang"
              class="form-control"
              placeholder="2..."
              v-model="form.telat_pulang"
            />
          </BCol>
          <BCol :lg="12" class="justify-self-end">
            <div class="mb-1 text-end">
              <BButton
                type="submit"
                :variant="dataEdit ? 'info' : 'primary'"
                @click.prevent="onSubmit"
                class="me-1"
              >
                <i class="ri-save-2-line me-1 align-bottom"></i>
                {{ dataEdit ? "Ubah" : "Tambah" }}
              </BButton>
              <BButton
                type="reset"
                variant="outline-secondary"
                @click="resetForm"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
                Reset
              </BButton>
            </div>
          </BCol>
        </BRow>
      </div>
    </BForm>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { mShiftService } from "@/services/MShiftService";
import { toastMethods, toastState } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";

const initForm = () => ({
  id: "",
  kode: "",
  nama: "",
  mulai_absen: "",
  jam_masuk: "",
  telat_masuk: "",
  jam_pulang: "",
  telat_pulang: "",
});

export default {
  components: {
    flatPickr,
  },
  setup() {
    return { v$: useVuelidate() };
  },
  watch: {
    "form.kode"(newValue) {
      this.form.kode = newValue.toString().toUpperCase();
    },
    "form.nama"(newValue) {
      this.form.nama = newValue.toString().toUpperCase();
    },
  },
  data() {
    return {
      form: initForm(),
      dataEdit: false,
    };
  },
  validations() {
    return {
      form: {
        kode: { required },
        nama: { required },
        mulai_absen: { required },
        jam_masuk: { required },
        telat_masuk: { required },
        jam_pulang: { required },
        telat_pulang: { required },
      },
    };
  },
  computed: {
    ...toastState,
  },
  created() {
    this.resetForm();
  },
  methods: {
    ...toastMethods,

    async onUpdate() {
      const [err] = await mShiftService.update(this.form, this.form.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.$emit("fetch");
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil diubah",
      });
      this.resetForm();
      this.$emit("close");
    },

    async onStore() {
      const [err] = await mShiftService.store(this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.$emit("fetch");
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil ditambahkan",
      });
      this.resetForm();
      this.$emit("close");
    },

    async onSubmit() {
      const result = await this.v$.$validate();
      console.log(result);
      if (!result) {
        this.toastError({
          title: "Gagal",
          msg: "Form wajib diisi",
        });
        return;
      }
      if (this.dataEdit) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },

    resetForm() {
      this.$data.form = initForm();
    },

    setUpdateData(params, editable) {
      this.dataEdit = editable;
      this.form.id = params?.id;
      this.form.kode = params?.kode;
      this.form.nama = params?.nama;
      this.form.mulai_absen = params?.mulai_absen;
      this.form.jam_masuk = params?.jam_masuk;
      this.form.telat_masuk = params?.telat_masuk;
      this.form.jam_pulang = params?.jam_pulang;
      this.form.telat_pulang = params?.telat_pulang;
    },
  },
};
</script>
