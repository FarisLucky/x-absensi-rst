<template>
  <div>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <p class="text-end m-0 mb-1">
        (<strong class="text-danger">*</strong>) tidak boleh kosong
      </p>
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">Data Izin</h5>
        <BRow class="g-2 mb-1 align-items-end">
          <BCol lg="2">
            <label for="kode" class="form-label">
              Kode
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="kode"
              class="form-control"
              placeholder="KODE CUTI..."
              v-model="form.kode"
              :disabled="dataEdit"
            />
          </BCol>
          <BCol lg="2">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="CUTI TAHUNAN..."
              v-model="form.nama"
            />
          </BCol>
          <BCol lg="2">
            <label for="acc1" class="form-label">
              ACC MANAJEMEN
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.acc_manajemen"
              :options="[
                {
                  id: 0,
                  val: 'TIDAK',
                },
                {
                  id: 1,
                  val: 'YA',
                },
              ]"
              :reduce="(th) => th.id"
              label="val"
              placeholder="Pilih ACC Manajemen"
            ></v-select>
          </BCol>
          <BCol lg="2">
            <label for="tahunan" class="form-label">
              Kurangi Cuti
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.tahunan"
              :options="[
                {
                  id: 0,
                  val: 'TIDAK',
                },
                {
                  id: 1,
                  val: 'YA',
                },
              ]"
              :reduce="(th) => th.id"
              label="val"
              placeholder="Pilih Tahunan"
            ></v-select>
          </BCol>
          <BCol>
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
          </BCol>
        </BRow>
      </div>
    </BForm>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { mIzinService } from "@/services/MIzinService";
import { toastMethods, toastState } from "@/state/helpers";

const initForm = () => ({
  id: "",
  kode: "",
  nama: "",
  acc_manajemen: 0,
  tahunan: 0,
});

export default {
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
        acc_manajemen: { required },
        tahunan: { required },
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
      const [err] = await mIzinService.update(this.form, this.form.id);
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
      const [err] = await mIzinService.store(this.form);
      this.progress = true;
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
      this.dataEdit = false;
    },

    resetForm() {
      this.$data.form = initForm();
    },

    setUpdateData(params, editable) {
      this.dataEdit = editable;
      this.form.id = params?.id;
      this.form.kode = params?.kode;
      this.form.nama = params?.nama;
      this.form.acc_manajemen = params?.acc_manajemen;
      this.form.tahunan = params?.tahunan;
    },
  },
};
</script>
