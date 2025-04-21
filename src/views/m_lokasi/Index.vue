<template>
  <Layout>
    <PageHeader title="lokasi" pageTitle="Pengaturan" />
    <div class="h-100">
      <BRow>
        <BCol xl="12">
          <BCard no-body>
            <BCardHeader class="border-0">
              <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Lokasi Absen</h5>
                <div class="flex-shrink-0">
                  <div class="d-flex flex-wrap gap-2">
                    <BButton variant="primary" @click.prevent="showModal">
                      <i class="ri-add-line align-bottom me-1"></i>
                      Tambah
                    </BButton>
                  </div>
                </div>
              </div>
            </BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <BForm>
                <BRow class="g-2">
                  <BCol xxl="3" sm="4">
                    <div class="search-box">
                      <input
                        type="text"
                        class="form-control search bg-light border-light"
                        placeholder="Cari Lokasi disini..."
                        v-model="filter.search"
                      />
                      <i class="ri-search-line search-icon"></i>
                    </div>
                  </BCol>
                  <BCol>
                    <BButton
                      type="button"
                      variant="outline-secondary"
                      @click="onRefresh"
                    >
                      <i class="ri-refresh-fill me-1 align-bottom"></i>
                      Reset
                    </BButton>
                  </BCol>
                </BRow>
              </BForm>
            </BCardBody>
            <BCardBody>
              <div class="mb-1">
                <vue-good-table
                  mode="local"
                  :columns="columns"
                  :rows="rows"
                  :select-options="{
                    enabled: true,
                    selectOnCheckboxOnly: true,
                  }"
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
                    <span v-if="props.column.field == 'status'">
                      <strong v-if="props.row.status === '1'">AKTIF</strong>
                      <strong v-else>NONAKTIF</strong>
                    </span>
                    <span v-if="props.column.field == 'action'">
                      <BButton
                        variant="soft-info"
                        class="me-1"
                        @click.prevent="onShow(props.row.id)"
                        size="sm"
                      >
                        <i class="ri-edit-2-fill"></i>
                      </BButton>
                      <BButton
                        variant="soft-danger"
                        @click.prevent="onDestroy(props.row.id)"
                        size="sm"
                      >
                        <i class="ri-delete-bin-6-fill"></i>
                      </BButton>
                    </span>
                  </template>
                </vue-good-table>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
    </div>
    <BModal
      v-model="modal"
      hide-footer
      header-class="p-3 bg-info-subtle"
      class="v-modal-custom"
      scrollable
      size="xl"
      title="Form Lokasi"
      no-close-on-backdrop
      @close="hideModal"
    >
      <div>
        <Tambah @fetch="fetchData" ref="formTambahRef" @close="hideModal" />
      </div>
    </BModal>
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import Tambah from "./Tambah.vue";
import { toastMethods } from "@/state/helpers";
import { mLokasiService } from "@/services/MLokasiService";

const initFilter = () => ({
  search: "",
});

export default {
  components: {
    Layout,
    PageHeader,
    VueGoodTable,
    Tambah,
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
          label: "Latitude",
          field: "latitude",
        },
        {
          label: "Longitude",
          field: "longitude",
        },
        {
          label: "Radius",
          field: "radius",
        },
        {
          label: "Status",
          field: "status",
          tdClass: (row) => {
            return row.status === "1"
              ? "bg-success text-light"
              : "bg-secondary text-white";
          },
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      isLoading: false,
      modal: false,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      const [err, resp] = await mLokasiService.all();
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
    async onShow(id) {
      const [err, resp] = await mLokasiService.show(id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      let formRef = this.$refs.formTambahRef;
      this.showModal();
      formRef.setUpdateData(resp.data, true);
    },
    async onDestroy(id) {
      if (confirm("Apakah ingin dihapus ?")) {
        const [err] = await mLokasiService.delete(id);
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
    showModal() {
      this.modal = true;
      this.$refs.formTambahRef.showMap = true;
      this.$refs.formTambahRef.dataEdit = false;
    },
    hideModal() {
      this.modal = false;
      this.$refs.formTambahRef.showMap = false;
      this.$refs.formTambahRef.resetForm();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
    },
  },
};
</script>
