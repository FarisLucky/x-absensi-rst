<template>
  <div class="position-relative">
    <div class="chat-wrapper d-lg-flex gap-1 p-1">
      <div class="file-manager-sidebar minimal-border">
        <div class="p-3 d-flex flex-column h-100">
          <div class="mb-3">
            <small class="text-danger">FITUR BELUM SELESAI</small>
            <h5 class="mb-0 fw-semibold mt-1">Dokumen Karyawan</h5>
          </div>
          <div class="search-box">
            <form autocomplete="off" @submit.prevent="">
              <div class="d-flex">
                <div>
                  <input
                    type="text"
                    class="form-control bg-light border-light"
                    placeholder="Cari Nama Karyawan..."
                    @input="searchKaryawan($event.target.value)"
                  />
                  <i class="ri-search-2-line search-icon"></i>
                </div>
                <BButton
                  variant="secondary"
                  @click.prevent="
                    () => {
                      karyawans = karyawansTemp;
                      filter.search_karyawan = '';
                    }
                  "
                >
                  <i class="ri-refresh-line"></i>
                </BButton>
              </div>
            </form>
          </div>
          <simplebar
            class="mt-3 mx-n4 px-4 file-menu-sidebar-scroll"
            data-simplebar
          >
            <ul class="list-unstyled file-manager-menu">
              <li>
                <BLink role="button" v-b-toggle.collapseExample>
                  <i class="ri-folder-2-line align-bottom me-2"></i>
                  <span class="file-list-link">Karyawan</span>
                </BLink>
                <BCollapse id="collapseExample" visible>
                  <ul class="sub-menu list-unstyled">
                    <li
                      v-for="(karyawan, idx) in karyawans"
                      :key="idx"
                      class="my-1"
                    >
                      <BLink
                        @click.prevent="updateTableFile(karyawan.nip)"
                        :active="karyawan.nip === nip"
                        >{{ karyawan?.nama }}</BLink
                      >
                    </li>
                  </ul>
                </BCollapse>
              </li>
            </ul>
          </simplebar>
        </div>
      </div>

      <div class="file-manager-content minimal-border w-100 p-3 py-0">
        <simplebar
          class="mx-n3 pt-4 px-4 file-manager-content-scroll"
          data-simplebar
        >
          <div>
            <div class="d-flex align-items-center mb-3">
              <h5 class="flex-grow-1 fs-16 mb-0" id="filetype-title">
                List Dokumen Karyawan
              </h5>
              <div class="flex-shrink-0">
                <BButton
                  variant="success"
                  class="create-folder-modal text-nowrap flex-shrink-0"
                  @click="openFileModal"
                  ><i class="ri-add-line align-bottom me-1"></i> Create
                  File</BButton
                >
              </div>
            </div>
            <div class="mb-3 search-box">
              <form autocomplete="off" @submit.prevent="">
                <input
                  type="text"
                  v-model="filter.search"
                  class="form-control bg-light border-light"
                  placeholder="Cari File"
                />
                <i class="ri-search-2-line search-icon"></i>
              </form>
            </div>
            <div class="mb-1">
              <vue-good-table
                mode="local"
                :columns="columns"
                :rows="rows"
                :pagination-options="{
                  enabled: true,
                }"
                :search-options="{
                  enabled: true,
                  externalQuery: filter.search,
                }"
                :line-numbers="true"
                :isLoading="isLoading"
                theme="polar-bear"
                styleClass="vgt-table"
              >
                <template #table-row="props">
                  <span v-if="props.column.field == 'action'">
                    <a
                      href="javascript(0)"
                      class="text-info me-1"
                      @click.prevent="onShow(props.row.id)"
                    >
                      <i class="ri-edit-2-fill"></i>
                    </a>
                    <a
                      class="text-danger"
                      href="javascript(0)"
                      @click.prevent="onDestroy(props.row.id)"
                    >
                      <i class="ri-delete-bin-6-fill"></i>
                    </a>
                  </span>
                </template>
              </vue-good-table>
            </div>
          </div>
        </simplebar>
      </div>
    </div>

    <BModal
      v-model="fileModal"
      hide-footer
      title="Upload Dokumen"
      title-class="exampleModalLabel"
      class="v-modal-custom"
      modal-class="zoomIn"
      centered
      header-class="p-3 bg-success-subtle"
    >
      <BForm
        autocomplete="off"
        class="needs-validation createfile-form"
        id="createfile-form"
        novalidate
      >
        <div class="mb-1">
          <label for="jenis" class="form-label">Jenis Dokumen</label>
          <v-select :options="listJenis" v-model="modalForm.jenis"></v-select>
        </div>
        <div class="mb-1">
          <label for="nama" class="form-label">Nama Dokumen</label>
          <input
            type="text"
            class="form-control"
            id="nama"
            v-model="modalForm.nama"
            required
            placeholder="Masukkan Nama Dokumen"
          />
        </div>
        <div class="mb-3">
          <label for="jenis" class="form-label">Jenis Dokumen</label>
          <FormUpload ref="fileUpload" />
        </div>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton
            type="button"
            variant="ghost-success"
            id="addFileBtn-close"
            @click="fileModal = false"
          >
            <i class="ri-close-line align-bottom"></i> Close
          </BButton>
          <BButton
            type="button"
            variant="primary"
            id="createfile-btn"
            @click="createNewfile"
          >
            Simpan
          </BButton>
        </div>
      </BForm>
    </BModal>
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { toastMethods } from "@/state/helpers";
import { mUnitService } from "@/services/MUnitService";
import simplebar from "simplebar-vue";
import { mKaryawanService } from "@/services/MKaryawanService";
import queryString from "query-string";
import { mKaryawanDokService } from "@/services/MKaryawanDokService";
import FormUpload from "@/components/form-upload.vue";

const initFilter = () => ({
  search: "",
  search_karyawan: "",
});

export default {
  components: {
    VueGoodTable,
    simplebar,
    FormUpload,
  },
  data() {
    return {
      filter: initFilter(),
      columns: [
        {
          label: "Nama",
          field: "nama",
        },
        {
          label: "Jenis",
          field: "jenis",
        },
        {
          label: "Ukuran",
          field: "size",
        },
        {
          label: "Ket",
          field: "ket",
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      isLoading: false,
      karyawans: [],
      karyawansTemp: [],
      listJenis: ["FILE KEPEGAWAIAN"],
      nip: "",
      fileModal: false,
      modalForm: {
        nama: "",
        jenis: "",
        nip: "",
      },
    };
  },
  created() {
    this.fetchData();
    this.getKaryawan();
  },
  methods: {
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      let query = queryString.stringify(
        { nip: this.nip },
        {
          arrayFormat: "index",
        }
      );

      const [err, resp] = await mKaryawanDokService.all(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.rows = resp.data;
      this.isLoading = false;
    },
    async getKaryawan() {
      const [err, resp] = await mKaryawanService.getNamaKaryawan();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.karyawans = resp.data;
      this.karyawansTemp = resp.data;
      if (this.karyawans.length > 0) {
        this.nip = this.karyawans[0]?.nip;
      }
    },
    async getJenis() {
      const [err, resp] = await mKaryawanService.allJenis();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.karyawans = resp.data;
      this.karyawansTemp = resp.data;
      if (this.karyawans.length > 0) {
        this.nip = this.karyawans[0]?.nip;
      }
    },
    searchKaryawan(params) {
      this.karyawans = this.karyawansTemp.filter((k) =>
        k.nama.toLowerCase().includes(params.toLowerCase())
      );
      this.filter.search_karyawan = params;
    },
    async onDestroy(id) {
      if (confirm("Apakah ingin dihapus ?")) {
        const [err] = await mUnitService.delete(id);
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          this.isLoading = false;

          return;
        }
        this.toastSuccess({
          title: "Berhasil",
          msg: "Tindakan berhasil",
        });
        this.fetchData();
      }
    },
    onCreate() {
      if (this.$refs.formUnitCollapseRef.visible) {
        this.closeForm();
        return;
      }
      this.openForm();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
    },
    onCustomFilter() {},
    openForm() {
      this.$refs.formUnitCollapseRef.open();
    },
    closeForm() {
      this.$refs.formUnitCollapseRef.close();
    },
    updateTableFile(nip) {
      this.nip = nip;
      this.fetchData();
    },
    openFileModal() {
      this.fileModal = true;
    },
    closeFileModal() {
      this.fileModal = true;
    },
    async createNewfile() {
      this.modalform.nip = this.nip;

      let formData = new FormData();
      formData.append("nip", this.nip);
      formData.append("nama", this.modalForm.nama);
      formData.append("nip", this.modalForm.nip);
      formData.append("jenis", this.modalForm.jenis);
      if (this.$refs.fileUpload?.myFiles[0] !== undefined) {
        formData.append("file", this.$refs.fileUpload?.myFiles[0]);
      }
      const [err] = await mKaryawanDokService.store(formData);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: "OK",
      });
      this.fileModal = false;
    },
  },
};
</script>
