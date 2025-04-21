<style>
.h-30 {
  min-height: 30vh !important;
}
</style>
<template>
  <BModal
    v-model="modal"
    hide-footer
    title="Modal Device"
    class="v-modal-custom"
    size="lg"
  >
    <div>
      <!-- <form method="post" @submit.prevent="onSubmit"> -->
      <BRow class="g-2 align-items-end">
        <BCol md="12">
          <hr class="my-2" />
          <h5>List Permintaan Reset Perangkat</h5>
          <vue-good-table
            mode="local"
            :columns="columns"
            :rows="rows"
            :pagination-options="{
              enabled: true,
            }"
            :line-numbers="true"
            :isLoading="isLoading"
            theme="polar-bear"
            styleClass="vgt-table"
          >
            <template #table-row="props">
              <span v-if="props.column.field == 'nama'">
                <div class="d-flex">
                  <img
                    v-if="props.row?.photo !== null"
                    :src="props.row?.photo_url_cast"
                    class="avatar-sm me-1 rounded material-shadow"
                  />
                  <img
                    v-else
                    src="@/assets/images/profil.jpg"
                    class="avatar-sm me-1 rounded material-shadow"
                    width="30px"
                  />
                  <div class="p-2">
                    <strong class="mb-1 d-block">{{ props.row?.nama }}</strong>
                    <div class="badge badge-gradient-info">
                      {{ props.row.m_unit?.nama }}
                    </div>
                  </div>
                </div>
              </span>
              <span
                v-if="
                  props.column.field == 'action' && props.row.status == null
                "
              >
                <a
                  href="javascript(0)"
                  class="btn btn-sm btn-soft-primary me-1"
                  @click.prevent="onSubmit(props.row)"
                >
                  <i class="ri-edit-2-fill"></i>
                  Terima
                </a>
                <a
                  class="btn btn-sm btn-soft-danger"
                  href="javascript(0)"
                  @click.prevent="onDestroy(props.row.id)"
                >
                  <i class="ri-delete-bin-6-fill"></i>
                  Tolak
                </a>
              </span>
            </template>
          </vue-good-table>
        </BCol>
      </BRow>
      <!-- </form> -->
    </div>
  </BModal>
</template>
<script>
import { mKaryawanService } from "@/services/MKaryawanService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";

const initForm = () => ({
  nip: "",
});

export default {
  components: {
    VueGoodTable,
  },
  data() {
    return {
      title: "Setting Jabatan",
      modal: false,
      modalForm: initForm(),
      progress: false,
      nip: null,
      rows: [],
      columns: [
        {
          label: "Nip",
          field: "nip",
        },
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Keterangan",
          field: "ket",
        },
        {
          label: "ID Lama",
          field: "from",
        },
        {
          label: "ID Baru",
          field: "to",
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      isLoading: false,
    };
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.nip = null;
      this.form = initForm();
      this.modal = false;
    },
    async fetchData() {
      this.show();
      const [err, resp] = await mKaryawanService.getReqDevices();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.rows = resp.data;
      this.hide();
    },
    async onSubmit(row) {
      this.show();
      const [err] = await mKaryawanService.updateDevice(row, row.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.hide();
      this.fetchData();
    },
    async onResetDevice() {
      this.show();
      const [err, resp] = await mKaryawanService.resetDevice(this.modalForm);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
      this.hide();
      this.fetchData();
    },
    async onDestroy(id) {
      if (confirm("Apakah ingin dihapus ?")) {
        this.show();
        const [err, resp] = await mKaryawanService.destoryReqDevice(id);
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          this.hide();
          return;
        }
        this.toastSuccess({
          title: "Berhasil",
          msg: resp.data,
        });
        this.hide();
        this.fetchData();
      }
    },
  },
};
</script>
