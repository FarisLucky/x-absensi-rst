<template>
  <BRow>
    <BCol xl="12">
      <BCard no-body>
        <BCardHeader class="border-0">
          <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">Jadwal Kerja</h5>
          </div>
        </BCardHeader>
        <BCardBody class="border border-dashed border-end-0 border-start-0">
          <form>
            <BRow class="g-2">
              <BCol lg="3">
                <div class="search-box">
                  <input
                    type="text"
                    class="form-control search bg-light border-light"
                    placeholder="Cari Karyawan disini..."
                    v-model="filter.search"
                  />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol lg="3">
                <v-select
                  v-model="filter.unit"
                  :options="unitList"
                  :reduce="(unit) => unit.id"
                  @option:selected="fetchData"
                  placeholder="Pilih Unit"
                  label="nama"
                ></v-select>
              </BCol>
              <BCol>
                <BButton
                  type="button"
                  variant="soft-secondary"
                  @click="onRefresh"
                  class="me-1"
                >
                  <i class="ri-refresh-fill me-1 align-bottom"></i>
                </BButton>
                <BButton
                  type="button"
                  variant="soft-primary"
                  @click="onShowJadwal"
                  class="me-1"
                >
                  <i class="ri-eye-fill me-1 align-bottom"></i>
                  Lihat
                </BButton>
                <BButton
                  v-if="isSuperAdmin"
                  type="button"
                  variant="soft-success me-1"
                  @click="onImport"
                >
                  <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
                  Import
                </BButton>
                <BButton
                  v-if="isSuperAdmin"
                  type="button"
                  variant="success me-1"
                  @click="scheduleNotification"
                >
                  <i class="ri-notification-3-line me-1 align-bottom"></i>
                  Alarm Jadwal
                </BButton>
              </BCol>
            </BRow>
          </form>
        </BCardBody>
        <BCardBody>
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
              styleClass="vgt-table striped sticky"
            >
              <template #table-row="props">
                <span v-if="props.column.field == 'nip'">
                  <router-link
                    :to="{
                      name: 'JadwalEdit',
                      params: { nip: props.row.nip },
                    }"
                    href="javascript(0)"
                    class="text-info me-1"
                  >
                    {{ props.row.nip }}
                  </router-link>
                </span>
                <span v-if="props.column.field == 'nama'">
                  <div class="d-flex">
                    <img
                      v-if="props.row.photo !== null"
                      :src="props.row.photo_url_cast"
                      class="avatar-sm me-1 rounded material-shadow"
                      width="30px"
                    />
                    <img
                      v-else
                      src="@/assets/images/profil.jpg"
                      class="avatar-sm me-1 rounded material-shadow"
                      width="30px"
                    />
                    <div class="p-2">
                      <router-link
                        :to="{
                          name: 'ProfileKaryawan',
                          params: {
                            nip: props.row.nip,
                          },
                        }"
                        class="d-block mb-1"
                      >
                        {{ props.row.nama }}
                        <i class="ri-search-eye-line"></i>
                      </router-link>
                      <div class="badge badge-gradient-info">
                        {{ props.row?.unit }}
                      </div>
                    </div>
                  </div>
                </span>
                <span v-if="props.column.field == 'action'">
                  <router-link
                    :to="{
                      name: 'JadwalEdit',
                      params: { nip: props.row.nip },
                    }"
                    href="javascript(0)"
                    class="btn btn-sm btn-soft-primary"
                  >
                    <i class="ri-play-line"></i>
                  </router-link>
                </span>
              </template>
            </vue-good-table>
          </div>
        </BCardBody>
      </BCard>
    </BCol>
    <Import ref="importJadwalRef" :units="unitList" />
    <Jadwal ref="lihatJadwalRef" :units="unitList" />
  </BRow>
</template>
<script>
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { toastMethods } from "@/state/helpers";
import { jadwalService } from "@/services/JadwalService";
import { SUPER_ADMIN } from "@/helpers/utils.js";
import { defineAsyncComponent } from "vue";
import { mUnitService } from "@/services/MUnitService";
import { notificationService } from "@/services/NotificationService";
import { useNotification } from "@/composable/useLocalNotification";

const initFilter = () => ({
  search: "",
  bawahan: "unit",
  unit: "",
});

export default {
  components: {
    VueGoodTable,
    Import: defineAsyncComponent(() => import("./Import")),
    Jadwal: defineAsyncComponent(() => import("./Jadwal")),
  },
  data() {
    return {
      filter: initFilter(),
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
          label: "Lahir",
          field: "tgl_lahir_cast",
        },
        {
          label: "Pendidikan",
          field: "pendidikan",
        },
        {
          label: "Jabatan",
          field: "nama_jabatan",
        },
        {
          label: "Aksi",
          field: "action",
        },
      ],
      rows: [],
      isLoading: false,
      unitList: [],
    };
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state?.auth?.data?.role === SUPER_ADMIN;
    },
  },
  created() {
    this.fetchData();
    this.getUnit();
  },
  methods: {
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      const [err, resp] = await jadwalService.all(this.filter);
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
    async getUnit() {
      const [err, resp] = await mUnitService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });

        return;
      }
      this.unitList = resp.data;
    },
    async onShowJadwal() {
      await this.$refs.lihatJadwalRef.onShowJadwal();
      this.$refs.lihatJadwalRef.showModal();
    },
    onRefresh() {
      this.filter = initFilter();
      this.fetchData();
    },
    onImport() {
      this.$refs.importJadwalRef.showModal();
    },
    async scheduleNotification() {
      const [err, resp] = await notificationService.getJadwal();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });

        return;
      }

      let jadwals = resp.data;

      if (!useNotification().isPermissionGranted) {
        await useNotification().requestPermission();
      }

      await useNotification().scheduleNotifications(jadwals);
    },
  },
};
</script>
