<style>
.data-karyawan {
  list-style-type: none;
  padding-left: 0;
}
.data-karyawan li span {
  display: inline-block;
  width: 5rem;
}
.data-karyawan li strong {
  padding: 0 0.3rem;
  margin-bottom: 4px;
  border-radius: 5px;
}
</style>
<template>
  <div>
    <BRow class="g-2">
      <BCol>
        <BCard no-body>
          <BCardBody class="border-0 h-100">
            <div class="d-flex justify-content-between mb-3">
              <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
                Data Jadwal
              </h5>
              <div>
                <BButton
                  variant="danger"
                  class="me-1"
                  @click.prevent="onCreate"
                >
                  <i class="ri-add-line align-bottom me-1"></i>
                  Tambah
                </BButton>
                <BButton
                  variant="outline-secondary"
                  @click.prevent="() => $router.back()"
                >
                  <i class="ri-arrow-left-fill me-1"></i>
                  Kembali
                </BButton>
              </div>
            </div>
            <div class="mb-1">
              <ul class="data-karyawan">
                <li>
                  <span>NIP</span>
                  <strong class="bg-light">{{ karyawan?.nip }}</strong>
                </li>
                <li>
                  <span>Nama</span>
                  <strong>{{ karyawan?.nama }}</strong>
                </li>
                <li>
                  <span>Tgl Lahir</span>
                  <strong class="bg-light">{{ karyawan?.tgl_lahir }}</strong>
                </li>
                <li>
                  <span>Staff</span>
                  <strong>{{ karyawan?.jabatans[0]?.m_jabatan?.nama }}</strong>
                </li>
              </ul>
            </div>
            <BCollapse id="formJadwalCollapse" ref="formJadwalCollapseRef">
              <div class="mb-2">
                <BRow class="g-2 align-items-end">
                  <div class="col-12">
                    <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
                      Form Jadwal
                    </h5>
                  </div>
                  <div class="col-lg-3">
                    <label for="tanggal" class="form-label">
                      Tanggal
                      <span class="text-danger">*</span>
                    </label>
                    <flat-pickr
                      v-model="form.tanggal"
                      placeholder="Pilih Tanggal"
                      :config="{
                        minDate: 'today',
                        dateFormat: 'Y-m-d',
                      }"
                      class="form-control bg-light border-light"
                    ></flat-pickr>
                  </div>
                  <div class="col-lg-3">
                    <label for="kode_shift" class="form-label">
                      Shift
                      <span class="text-danger">*</span>
                    </label>
                    <v-select
                      v-model="form.kode_shift"
                      :options="listShift"
                      :reduce="(st) => st.kode"
                      label="nama"
                      placeholder="Pilih Shift (Masuk - Pulang)"
                    ></v-select>
                  </div>
                  <div class="col-lg">
                    <BButton
                      type="submit"
                      variant="outline-primary"
                      @click.prevent="onSubmit"
                      class="me-1"
                    >
                      <i class="ri-save-2-line me-1 align-bottom"></i>
                      Simpan
                    </BButton>
                  </div>
                </BRow>
              </div>
            </BCollapse>
            <BRow
              class="g-2 align-items-end mb-2 border border-dashed border-end-0 border-start-0 py-2 border-bottom-0"
            >
              <BCol lg="3">
                <div class="search-box">
                  <input
                    type="text"
                    class="form-control search bg-light border-light"
                    placeholder="Cari Jadwal disini..."
                    v-model="filter.search"
                  />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol>
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
            <div class="mb-1 table-responsive">
              <table class="table table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Shift</th>
                    <th>Mulai</th>
                    <th>Masuk</th>
                    <th>Terlambat</th>
                    <th>Pulang</th>
                    <th>Absen Pulang</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(jadwal, idx) in jadwals" :key="idx">
                    <td>{{ jadwal.tanggal }}</td>
                    <td>{{ jadwal.kode_shift }}</td>
                    <td>
                      <v-select
                        v-model="jadwal.kode_shift"
                        :options="listShift"
                        :reduce="(st) => st.kode"
                        label="nama"
                        placeholder="Pilih Shift (Masuk - Pulang)"
                        @option:selected="
                          onUpdateJadwal(
                            {
                              id: jadwal.id,
                              kode_shift: jadwal.kode_shift,
                            },
                            jadwal.id
                          )
                        "
                      ></v-select>
                    </td>
                    <td>
                      <strong>{{ jadwal.mulai_absen }}</strong> menit
                    </td>
                    <td>{{ jadwal.jam_masuk }}</td>
                    <td>
                      <strong>{{ jadwal.telat_masuk }}</strong> menit
                    </td>
                    <td>{{ jadwal.jam_pulang }}</td>
                    <td>
                      <strong>{{ jadwal.telat_pulang }}</strong> menit
                    </td>
                    <td>
                      <a
                        href="javascript(0)"
                        class="text-danger"
                        @click.prevent="onDelete(jadwal.id)"
                      >
                        <div class="ri-delete-bin-fill"></div>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { toastMethods } from "@/state/helpers";
import flatPickr from "vue-flatpickr-component";
import { mShiftService } from "@/services/MShiftService";
import { jadwalService } from "@/services/JadwalService";
import { mKaryawanService } from "@/services/MKaryawanService";

const initForm = () => ({
  id: "",
  nip: "",
  tanggal: "",
  kode_shift: "",
  type_tanggal: "harian",
});

export default {
  components: {
    flatPickr,
  },
  data() {
    return {
      form: initForm(),
      submitted: false,
      dataEdit: false,
      listShift: [],
      listKaryawan: [],
      flatConf: {
        minDate: "today",
        dateFormat: "Y-m-d",
      },
      jadwals: [],
      filter: {
        search: "",
      },
      karyawan: null,
    };
  },
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    return {
      form: {
        tanggal: { required },
        kode_shift: { required },
      },
    };
  },
  watch: {
    "filter.search"(newValue) {
      if (newValue !== "") {
        this.jadwals = this.jadwals.filter((jadwal) =>
          jadwal.includes(newValue)
        );
      } else {
        this.onShowByNip();
      }
    },
    setup() {
      return { v$: useVuelidate() };
    },
  },
  created() {
    this.form.nip = this.$route.params.nip;
    Promise.all([this.getShift(), this.getKaryawan(), this.onShowByNip()]);
  },
  methods: {
    ...toastMethods,

    async onShowByNip() {
      this.resetJadwal();
      const [err, resp] = await jadwalService.showByNip(this.$route.params.nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.jadwals = resp.data;
    },
    async getShift() {
      const [err, resp] = await mShiftService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.listShift = resp.data;
    },

    async getKaryawan() {
      const [err, resp] = await mKaryawanService.show(this.$route.params.nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.karyawan = resp.data;
    },

    async onDelete(id) {
      if (confirm("Hapus jadwal ?")) {
        const [err] = await jadwalService.delete(id);
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          return;
        }
        this.onShowByNip();
      }
    },

    async onSubmit() {
      const result = await this.v$.$validate();
      if (!result) {
        this.toastError({
          title: "Gagal",
          msg: "Form wajib diisi",
        });
        return;
      }

      const [err] = await jadwalService.store(this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.onShowByNip();
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil ditambahkan",
      });
      this.resetForm();
      this.$emit("close");
    },

    resetForm() {
      this.$data.form = initForm();
      Promise.all([this.getShift(), this.onShowByNip()]);
    },
    resetJadwal() {
      this.jadwals = [];
    },
    onCreate() {
      if (this.$refs.formJadwalCollapseRef.visible) {
        this.closeForm();
        return;
      }
      this.openForm();
    },
    openForm() {
      this.$refs.formJadwalCollapseRef.open();
    },
    closeForm() {
      this.$refs.formJadwalCollapseRef.close();
    },
    async onUpdateJadwal(form, id) {
      const [err] = await jadwalService.update(form, id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil diubah",
      });
    },
  },
};
</script>
