<template>
  <div>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <p class="text-end m-0 mb-1">
        (<strong class="text-danger">*</strong>) tidak boleh kosong
      </p>
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
          Data Lembur
        </h5>
        <BRow class="g-2 mb-1">
          <BCol lg="2">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="Masukkan Nama"
              v-model="form.nama"
            />
          </BCol>
          <BCol lg="2">
            <label for="ttl_jam" class="form-label"> Total Jam </label>
            <input
              type="number"
              id="ttl_jam"
              class="form-control"
              placeholder="Jumlah Jam"
              v-model="form.ttl_jam"
            />
          </BCol>
          <BCol lg="2">
            <label for="longitude" class="form-label">
              Harga
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="longitude"
              class="form-control"
              placeholder="Harga."
              :value="form.harga"
              @input="format"
            />
          </BCol>
          <BCol lg="2">
            <label for="radius" class="form-label"> ID Lokasi </label>
            <v-select
              v-model="form.id_lokasi"
              :options="lokasiList"
              :reduce="(l) => l.id"
              label="nama"
              placeholder="Pilih Lokasi Absen"
            ></v-select>
          </BCol>
          <BCol lg="2">
            <label for="radius" class="form-label"> Absen Foto </label>
            <v-select
              v-model="form.absen_foto"
              :options="['YA', 'TIDAK']"
              placeholder="Pilih Absen Foto"
            ></v-select>
          </BCol>
          <BCol lg="2">
            <label for="status" class="form-label">
              Status
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.status"
              :options="[
                {
                  id: 1,
                  val: 'AKTIF',
                },
                {
                  id: 0,
                  val: 'NONAKTIF',
                },
              ]"
              :reduce="(st) => st.id"
              label="val"
              placeholder="Pilih Status Aktif"
            ></v-select>
          </BCol>
          <BCol>
            <label for="anggota" class="form-label"> Anggota </label>
            <input type="number" class="form-control" v-model="form.anggota" />
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

      <div class="hstack gap-2 justify-content-end mt-3"></div>
    </BForm>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { spinnerMethods, toastMethods, toastState } from "@/state/helpers";
import { mLemburService } from "@/services/MLemburService";
import { formatInput, parseFormatInput } from "@/helpers/format";
import { mLokasiService } from "@/services/MLokasiService";

const initForm = () => ({
  id: "",
  nama: "",
  ttl_jam: "",
  harga: "",
  jenis_absen: "",
  id_lokasi: "",
  absen_foto: "",
  status: "",
  anggota: 0,
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
      submitted: false,
      timeConfig: {
        enableTime: false,
        dateFormat: "d M, Y",
      },
      dataEdit: false,
      lokasiList: [],
    };
  },
  validations() {
    return {
      form: {
        nama: { required },
        harga: { required },
        status: { required },
      },
    };
  },
  computed: {
    ...toastState,
  },
  created() {
    this.resetForm();
    this.getLokasi();
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,

    async getLokasi() {
      this.show();

      const [err, resp] = await mLokasiService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.lokasiList = resp.data;
      this.hide();
    },
    async onUpdate() {
      this.form.harga = parseFormatInput(this.form.harga);
      const [err] = await mLemburService.update(this.form, this.form.id);
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
      this.form.harga = parseFormatInput(this.form.harga);
      const [err] = await mLemburService.store(this.form);
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
      this.dataEdit = false;
    },

    setUpdateData(params, editable) {
      this.dataEdit = editable;
      this.form.id = params?.id;
      this.form.nama = params?.nama;
      this.form.ttl_jam = params?.ttl_jam;
      this.form.harga = parseFormatInput(params?.harga?.toString());
      this.form.id_lokasi = params?.id_lokasi;
      this.form.absen_foto = params?.absen_foto;
      this.form.status = params?.status;
      this.form.anggota = params?.anggota;
    },
    format(event) {
      let harga = parseFormatInput(event.target.value);
      if (event.target.value === "") {
        this.form.harga = 0;
        return;
      } else if (isNaN(harga)) {
        alert("Wajib Angka");
        return;
      }
      this.form.harga = formatInput(harga.toString());
    },
  },
};
</script>
