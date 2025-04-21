<template>
  <div>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <p class="text-end m-0 mb-1">
        (<strong class="text-danger">*</strong>) tidak boleh kosong
      </p>
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">Data Unit</h5>
        <BRow class="g-2 mb-1 align-items-end">
          <BCol lg="3">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="General..."
              v-model="form.nama"
            />
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
import { toastMethods, toastState } from "@/state/helpers";
import { mUnitService } from "@/services/MUnitService";

const initForm = () => ({
  id: "",
  nama: "",
});

export default {
  setup() {
    return { v$: useVuelidate() };
  },
  watch: {
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
        nama: { required },
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
      const [err] = await mUnitService.update(this.form, this.form.id);
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
      const [err] = await mUnitService.store(this.form);
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
      this.form.nama = params?.nama;
    },
  },
};
</script>
