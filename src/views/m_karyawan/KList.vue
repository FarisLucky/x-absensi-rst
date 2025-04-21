<template>
  <div class="h-100">
    <BCard no-body>
      <BCardHeader>
        <div class="d-flex justify-content-between mb-1">
          <h5 class="fs-16 d-inline-block">Master Karyawan</h5>
          <router-link
            :to="{
              name: 'KaryawanTambah',
            }"
            class="btn btn-primary waves-effect waves-light"
          >
            <i class="ri-add-line align-bottom me-1"></i>
            Tambah
          </router-link>
        </div>
      </BCardHeader>
      <BCardBody>
        <form autocomplete="off" class="mb-1">
          <BRow class="g-2">
            <BCol lg="2">
              <div class="search-box">
                <input
                  type="text"
                  class="form-control search bg-light border-light"
                  placeholder="Cari Karyawan..."
                  @input="onFilterSearchFn"
                  :value="filter.search"
                  @keyup.enter="fetchData"
                />
                <i class="ri-search-line search-icon"></i>
              </div>
              <small class="mb-0 text-danger">
                <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
                Enter untuk mencari
              </small>
            </BCol>
            <BCol cols="6" lg="4">
              <v-select
                :modelValue="filter.unit"
                :options="listUnit"
                :reduce="(unit) => unit.id"
                label="nama"
                placeholder="Pilih Unit"
                @update:modelValue="onFilterUnitFn"
              ></v-select>
            </BCol>
            <BCol cols="6" md="2">
              <v-select
                :modelValue="filter.resign"
                :options="['RESIGN', 'AKTIF']"
                label="nama"
                placeholder="Pilih Resign"
                @update:modelValue="onFilterResignFn"
              ></v-select>
            </BCol>
            <BCol>
              <button
                type="button"
                class="btn btn-info btn-label waves-effect waves-light me-1"
                @click="fetchData"
              >
                <i class="ri-search-eye-line me-1 align-middle label-icon"></i>
                Cari
              </button>
              <BButton
                type="button"
                variant="soft-secondary"
                class="me-1"
                @click="onRefresh"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
              </BButton>
              <BButton
                type="button"
                variant="soft-success"
                class="me-1"
                @click="exportExcel"
              >
                <i class="ri-file-excel-2-line me-1 align-bottom"></i>
              </BButton>
              <BButton
                type="button"
                variant="soft-warning"
                class="me-1"
                @click="showDevice"
              >
                <i class="ri-shield-keyhole-fill me-1 align-bottom"></i>
                Perangkat
              </BButton>
            </BCol>
          </BRow>
        </form>
        <div class="mb-1 table-responsive">
          <vue-good-table
            mode="remote"
            v-on:page-change="onPageChange"
            v-on:sort-change="onSortChange"
            v-on:per-page-change="onPerPageChange"
            :totalRows="totalRecords"
            :columns="columns"
            :rows="rows"
            :select-options="{
              enabled: true,
              selectOnCheckboxOnly: true,
              selectionText: 'pilih maksimal 30 karyawan',
            }"
            :pagination-options="{
              enabled: true,
            }"
            :search-options="{
              enabled: true,
              externalQuery: filter.search,
            }"
            :isLoading="isLoading"
            :sort-options="{
              enabled: false,
            }"
            v-on:selected-rows-change="onSelect"
            theme="polar-bear"
            styleClass="vgt-table striped sticky"
            ref="myTable"
          >
            <template #table-row="props">
              <span v-if="props.column.field == 'nip'">
                <strong
                  class="me-1 text-success"
                  @click.prevent="copyText(props.row.nip.toString())"
                  style="cursor: pointer"
                  >{{ props.row.nip }}</strong
                >
                <i
                  v-if="props.row?.user?.nip !== undefined"
                  class="ri-check-line text-success"
                ></i>
              </span>
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
                    <strong class="mb-1 d-block">
                      {{ props.row?.nama }}
                    </strong>
                    <div class="badge badge-gradient-info">
                      {{ props.row.unit }}
                    </div>
                  </div>
                </div>
              </span>
              <span v-if="props.column.field == 'cuti'">
                <div v-if="isSuperAdmin">
                  <form autocomplete="off">
                    <input
                      v-model="props.row.cuti"
                      type="number"
                      class="form-control form-control-sm"
                      @keyup.enter="
                        onUpdateCuti({
                          nip: props.row.nip,
                          cuti: props.row.cuti,
                        })
                      "
                      :disabled="props.row.tgl_resign !== null"
                    />
                  </form>
                </div>
                <div v-else>
                  {{ props.row.cuti }}
                </div>
              </span>
              <span v-if="props.column.field == 'jabatan'">
                <span class="p-1 d-block">
                  {{ props.row.jabatan }}
                </span>
                <span
                  v-if="props.row.tgl_resign !== null"
                  class="badge bg-danger-subtle text-danger fs-10"
                >
                  Resign
                </span>
              </span>
              <span v-if="props.column.field == 'action'">
                <div>
                  <router-link
                    :to="{
                      name: 'KaryawanEdit',
                      params: {
                        nip: props.row.nip,
                      },
                    }"
                    class="btn btn-sm btn-soft-primary me-1 mb-1"
                    v-b-tooltip="'edit data'"
                  >
                    <i class="ri-play-line"></i>
                  </router-link>
                  <router-link
                    :to="{
                      name: 'JadwalEdit',
                      params: {
                        nip: props.row.nip,
                      },
                    }"
                    class="btn btn-sm btn-soft-info me-1 mb-1"
                    v-b-tooltip="'Lihat Jadwal'"
                  >
                    <i class="ri-calendar-check-fill"></i>
                  </router-link>
                  <a
                    v-if="props.row.tgl_resign === null && isSuperAdmin"
                    class="btn btn-sm btn-soft-danger"
                    href="javascript(0)"
                    @click.prevent="onResign(props.row.nip)"
                    v-b-tooltip="'Sudah Resign ?'"
                  >
                    <i class="ri-logout-box-r-line"></i>
                  </a>
                </div>
              </span>
            </template>
            <template #selected-row-actions>
              <BButton variant="soft-success" @click.prevent="createUser">
                <i class="ri-login-box-line"></i>
                Buat User Login
              </BButton>
            </template>
          </vue-good-table>
        </div>
      </BCardBody>
    </BCard>
    <CreateUser ref="createUserRef" @fetch="fetchData" />
    <ModalDetail ref="modalDetailRef" @fetch="fetchData" />
    <ModalResign ref="modalResignRef" @fetch="fetchData" />
    <ModalDevice ref="modalDeviceRef" />
  </div>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { mKaryawanService } from "@/services/MKaryawanService";
import { mUnitService } from "@/services/MUnitService";
import {
  toastMethods,
  spinnerMethods,
  karyawanMethods,
  karyawanState,
} from "@/state/helpers";
import male from "@/assets/images/male.png";
import female from "@/assets/images/female.png";
import useClipboard from "vue-clipboard3";
import { SUPER_ADMIN } from "@/helpers/utils";
import { defineAsyncComponent } from "vue";
import queryString from "query-string";
import { webUrl } from "@/config/http";

export default {
  components: {
    VueGoodTable,
    CreateUser: defineAsyncComponent(() => import("./CreateUser.vue")),
    ModalDetail: defineAsyncComponent(() => import("./ModalDetail.vue")),
    ModalResign: defineAsyncComponent(() => import("./ModalResign.vue")),
    ModalDevice: defineAsyncComponent(() => import("./ModalDevice.vue")),
  },
  data() {
    return {
      male,
      female,
      columns: [
        {
          label: "NIP / User",
          field: "nip",
        },
        {
          label: "Nama",
          field: "nama",
          width: "20%",
        },
        {
          label: "Sex",
          field: "sex",
        },
        {
          label: "Tgl Lahir",
          field: "tgl_lahir_cast",
        },
        {
          label: "Jabatan",
          field: "jabatan",
        },
        {
          label: "Alamat",
          field: "alamat",
        },
        {
          label: "Cuti",
          field: "cuti",
          width: "7%",
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      totalRecords: 0,
      pModal: false,
      listUnit: [],
      listJabatan: [],
      isLoading: false,
      selected: [],
    };
  },
  created() {
    this.fetchData();
    this.getListUnit();
  },
  computed: {
    ...karyawanState,
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    ...karyawanMethods,
    async fetchData() {
      this.isLoading = true;
      this.show();
      let query = queryString.stringify(
        Object.assign({}, this.filter, this.server),
        {
          arrayFormat: "index",
        }
      );
      const [err, resp] = await mKaryawanService.all(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        this.isLoading = false;

        return;
      }
      this.hide();
      this.isLoading = false;
      let pagination = resp.data;
      this.rows = pagination.data;
      this.totalRecords = pagination.total;
    },
    async getListUnit() {
      this.isLoading = true;

      const [err, resp] = await mUnitService.data();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.listUnit = resp.data;
      this.isLoading = false;
    },
    async onShow(id) {
      const [err, resp] = await mKaryawanService.show(id);
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
      let editable = true;
      formRef.setUpdateData(resp.data, editable);
    },
    onResign(nip) {
      this.$refs.modalResignRef.nip = nip;
      this.$refs.modalResignRef.showModal();
    },
    onSelect(params) {
      this.selected = params.selectedRows;
    },
    onRefresh() {
      this.resetFilter();
      this.fetchData();
      this.$refs.myTable.reset();
    },
    createUser() {
      let modalRef = this.$refs.createUserRef;
      modalRef.karyawan = this.selected.map((select) => ({
        nama: select.nama,
        nip: select.nip,
      }));
      modalRef.showModal();
    },
    copyText(txt) {
      const { toClipboard } = useClipboard();
      toClipboard(txt);
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil disalin",
      });
    },
    async onUpdateCuti(params) {
      if (parseInt(params.cuti) > 12 || parseInt(params.cuti) < 0) {
        this.toastError({
          title: "Gagal",
          msg: "maksimal 12",
        });

        return;
      }
      const [err, resp] = await mKaryawanService.updateCuti(params);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
    },
    async onUpdateSession(nip) {
      const [err, resp] = await mKaryawanService.updateSession(nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.toastSuccess({
        title: "Berhasil",
        msg: resp.data,
      });
    },
    showModalDetail(nip) {
      this.$refs.modalDetailRef.nip = nip;
      this.$refs.modalDetailRef.onShow();
      this.$refs.modalDetailRef.showModal();
    },
    onFilterSearchFn(event) {
      let val = event.target.value;
      if (val.length > 2) {
        this.onFilterSearch(val);
      }
      if (val === "") {
        this.onFilterSearch("");
      }
    },
    onFilterRangeFn(val) {
      this.onFilterRange(val);
    },
    onFilterUnitFn(val) {
      this.onFilterUnit(val);
    },
    onFilterResignFn(val) {
      this.onFilterResign(val);
    },
    exportExcel() {
      let query = queryString.stringify(Object.assign({}, this.filter), {
        arrayFormat: "index",
      });
      let url = `${webUrl}/m-karyawan/export?${query}`;
      const a = document.createElement("a");
      a.href = url;
      a.setAttribute("target", "_blank");
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    },
    showDevice() {
      this.$refs.modalDeviceRef.fetchData();
      this.$refs.modalDeviceRef.showModal();
    },
  },
};
</script>
