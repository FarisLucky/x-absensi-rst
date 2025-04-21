<template>
  <BCard no-body>
    <BCardBody>
      <p>Filter</p>
      <form autocomplete="off" class="mb-3">
        <BRow class="g-2 mb-1">
          <BCol cols="4" md="3">
            <v-select
              v-model="filter.year"
              :options="years"
              placeholder="Pilih Tahun"
            ></v-select>
          </BCol>
          <BCol cols="5" md="3">
            <v-select
              v-model="filter.month"
              :options="months"
              :reduce="(l) => l.id"
              label="name"
              placeholder="Pilih Bulan"
              @option:selected="fetchData"
            ></v-select>
          </BCol>
          <BCol>
            <BButton variant="soft-secondary" @click.prevent="onReset">
              <i class="ri-refresh-fill"></i>
            </BButton>
          </BCol>
        </BRow>
      </form>
      <div v-if="progress" class="p-2">
        <h5 class="text-center">Masih Loading...</h5>
      </div>
      <div v-else-if="!progress && jadwals.length < 1" class="p-2">
        <h5 class="text-center py-5">Tidak Ada Data</h5>
      </div>
      <simplebar data-simplebar style="height: calc(100vh - 112px)">
        <BRow class="g-2">
          <BCol cols="12" md="3" v-for="(jadwalku, idx) in jadwals" :key="idx">
            <div class="p-2 rounded border h-100">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <div class="ms-2">
                    <div class="p-1">
                      <ul class="list-group list-group-flush p-1">
                        <li class="list-group-item">
                          Jadwal:
                          <strong class="d-block">
                            {{ jadwalku?.tanggal_cast }}
                            <small class="me-1">:</small>
                            <span
                              v-if="jadwalku?.kode_shift !== null"
                              class="p-1 bg-secondary bg-opacity-25 rounded"
                            >
                              {{ jadwalku?.jam_masuk ?? "" }}
                              <i
                                class="ri-arrow-right-fill fs-12 lh-1 me-1"
                              ></i>
                              {{ jadwalku?.jam_pulang ?? "" }}
                            </span>
                            <span v-else> Libur </span>
                          </strong>
                        </li>
                        <li
                          v-if="
                            jadwalku?.status !== null &&
                            [1, 2].includes(jadwalku?.status)
                          "
                          class="list-group-item"
                        >
                          <BRow v-if="jadwalku?.masuk !== null" class="g-2">
                            <BCol cols="4">
                              <span class="d-block">Masuk</span>
                              <strong>
                                {{ jadwalku?.masuk ?? "" }}
                              </strong>
                            </BCol>
                            <BCol cols="4">
                              <span class="d-block">Pulang</span>
                              <strong>
                                {{ jadwalku?.pulang ?? "" }}
                              </strong>
                            </BCol>
                            <BCol cols="4">
                              <span class="d-block">Status</span>
                              <strong
                                class="p-1 bg-opacity-25 rounded d-inline-block"
                                :class="{
                                  'bg-warning':
                                    jadwalku?.presensi_status === 'TELAT',
                                  'bg-success':
                                    jadwalku?.presensi_status === 'TEPAT',
                                }"
                              >
                                {{ jadwalku?.presensi_status }}
                              </strong>
                              <i
                                v-if="jadwalku?.presensi_status === 'TELAT'"
                                class="ri-information-line fs-14"
                                v-b-tooltip="
                                  jadwalku?.presensi?.terlambat_ket ??
                                  'Tidak ada keterangan Terlambat'
                                "
                                style="cursor: pointer"
                              ></i>
                            </BCol>
                          </BRow>
                          <div v-else>
                            <span class="d-block">Status</span>
                            <strong
                              v-if="jadwalku?.status === 3"
                              class="p-1 bg-danger bg-opacity-25 rounded"
                            >
                              TIDAK HADIR
                            </strong>
                            <strong
                              v-else-if="jadwalku?.status === 4"
                              class="p-1 bg-info bg-opacity-25 rounded"
                            >
                              IZIN
                            </strong>
                          </div>
                        </li>
                        <li
                          v-if="
                            [
                              JADWAL_STATUS.SELESAI,
                              JADWAL_STATUS.PROGRESS,
                              JADWAL_STATUS.IZIN,
                            ].includes(jadwalku?.status)
                          "
                          class="list-group-item"
                        >
                          <BButton
                            variant="primary"
                            size="sm"
                            @click.prevent="onDetail(jadwalku)"
                          >
                            <i class="ri-arrow-right-line"></i>
                            Lihat
                          </BButton>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </BCol>
        </BRow>
      </simplebar>
    </BCardBody>
  </BCard>
</template>
<script>
import { jadwalService } from "@/services/JadwalService";
import { progressMethods, spinnerMethods, toastMethods } from "@/state/helpers";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { getYears, JADWAL_STATUS, months } from "@/helpers/utils";
import queryString from "query-string";

const initFilter = () => {
  const currentTime = new Date();
  return {
    month: currentTime.getMonth() + 1,
    year: currentTime.getFullYear(),
    day: "",
  };
};

export default {
  data() {
    return {
      jadwals: [],
      filter: initFilter(),
      progress: false,
      years: [],
      months,
      days: [],
      JADWAL_STATUS,
    };
  },
  created() {
    this.years = getYears();
    this.days = this.getDatesInMonth(this.filter.year, this.filter.month);
    this.fetchData();
  },
  watch: {
    "filter.month"(month) {
      this.days = this.getDatesInMonth(this.filter.year, month);
    },
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    ...progressMethods,
    async fetchData() {
      this.progress = true;
      this.jadwals = [];
      const query = queryString.stringify(Object.assign({}, this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await jadwalService.jadwalku(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.progress = false;
        this.listPresensi = [];

        return;
      }

      this.jadwals = resp.data;
      this.progress = false;
    },
    getDatesInMonth(year, month) {
      const date = new Date(year, month - 1, 1); // Bulan dimulai dari 0 (Januari = 0)
      const dates = [];

      while (date.getMonth() === month - 1) {
        // Format tanggal ke YYYY-MM-DD
        date.setDate(date.getDate() + 1); // Pindah ke tanggal berikutnya
        const formattedDate = date.toISOString().split("-")[2].split("T")[0];
        dates.push(formattedDate); // Tambahkan tanggal ke array
      }

      return dates;
    },
    onReset() {
      this.filter = initFilter();
      this.fetchData();
    },
    onDetail(jadwal) {
      if (
        [JADWAL_STATUS.SELESAI, JADWAL_STATUS.PROGRESS].includes(jadwal.status)
      ) {
        console.log(jadwal.id);
        this.$router.push({
          name: "PresensiDetail",
          params: { id: jadwal.id },
        });
      } else if (jadwal.status === JADWAL_STATUS.IZIN) {
        this.$router.push({
          name: "HistoryIzinDetail",
          params: { id: jadwal.id_izin },
        });
      }
    },
  },
};
</script>
