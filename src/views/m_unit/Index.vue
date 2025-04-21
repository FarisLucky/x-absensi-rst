<template>
  <Layout>
    <PageHeader title="Unit" pageTitle="Master" />
    <div class="h-100">
      <BRow>
        <BCol xl="12">
          <BCard no-body>
            <BCardHeader class="border-0">
              <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Master Unit</h5>
                <div class="flex-shrink-0">
                  <div class="d-flex flex-wrap gap-2">
                    <BButton variant="soft-primary" @click.prevent="onCreate">
                      <i class="ri-add-line align-bottom me-1"></i>
                      Tambah
                    </BButton>
                  </div>
                </div>
              </div>
            </BCardHeader>
            <BCollapse id="formUnitCollapse" ref="formUnitCollapseRef">
              <div
                class="mb-1 border border-dashed border-end-0 border-start-0 px-3 py-2 border-bottom-0"
              >
                <Tambah
                  @fetch="fetchData"
                  ref="formTambahRef"
                  @close="closeForm"
                  @open="openForm"
                />
              </div>
            </BCollapse>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <BForm>
                <BRow class="g-2">
                  <BCol xxl="3" sm="4">
                    <div class="search-box">
                      <input
                        type="text"
                        class="form-control search bg-light border-light"
                        placeholder="Cari Unit disini..."
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
                    <span v-if="props.column.field == 'tahunan'">
                      <span v-if="props.row.tahunan === '1'">YA</span>
                      <span v-else>TIDAK</span>
                    </span>
                    <span v-if="props.column.field == 'action'">
                      <BButton
                        href="javascript(0)"
                        class="me-1"
                        @click.prevent="onShow(props.row.id)"
                        variant="soft-info"
                        size="sm"
                      >
                        <i class="ri-edit-2-fill"></i>
                      </BButton>
                      <BButton
                        @click.prevent="onDestroy(props.row.id)"
                        variant="soft-danger"
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
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import Tambah from "./Tambah.vue";
import { toastMethods } from "@/state/helpers";
import { mUnitService } from "@/services/MUnitService";

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
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      isLoading: false,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      const [err, resp] = await mUnitService.all();
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
      const [err, resp] = await mUnitService.show(id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.openForm();
      let formRef = this.$refs.formTambahRef;
      formRef.setUpdateData(resp.data, true);
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
  },
};
</script>
