<template>
  <BCard class="h-100">
    <div class="d-flex justify-content-between">
      <span class="fs-13">{{ jadwal?.tanggal_for_human ?? "-" }}</span>
      <span class="fs-12">
        {{ jadwal?.shift }}
      </span>
    </div>
    <div class="text-end">
      <small class="text-danger">
        {{ `${jadwal?.jam_masuk ?? ""} - ${jadwal?.jam_pulang ?? ""}` }}
      </small>
    </div>
    <div class="px-2">
      <BRow>
        <BCol cols="8">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="fs-13">Masuk</span>
              <p class="mb-1 fs-18 text-muted">
                {{ jadwal?.masuk ?? "-" }}
              </p>
            </div>
            <div>
              <i class="ri-exchange-box-line fs-22"></i>
            </div>
            <div>
              <span class="fs-13">Pulang</span>
              <p class="mb-1 fs-18 text-muted">
                {{ jadwal?.pulang ?? "-" }}
              </p>
            </div>
          </div>
          <div class="p-1">
            <div v-if="jenis === 'LEMBUR'">
              <router-link
                v-if="jadwal?.masuk === null && jenis === 'LEMBUR'"
                :to="{ name: 'PengajuanLembur' }"
                class="btn btn-success waves-effect waves-light w-100"
              >
                <i class="ri-map-pin-line"></i>
                Masuk
              </router-link>
              <router-link
                v-else-if="
                  jadwal?.masuk !== null &&
                  jadwal?.keluar === null &&
                  jenis === 'LEMBUR'
                "
                :to="{ name: 'PengajuanLembur' }"
                class="btn btn-danger waves-effect waves-light w-100"
              >
                <i class="ri-map-pin-line"></i>
                Pulang
              </router-link>
              <div v-else class="badge badge-gradient-success w-100">
                <span class="fs-16"> Selesai </span>
              </div>
            </div>
            <div v-if="jenis === 'DINAS'">
              <router-link
                v-if="
                  jadwal?.kode_shift !== null &&
                  jadwal?.status === null &&
                  jenis === 'DINAS'
                "
                :to="{ name: 'PresensiMain' }"
                class="btn btn-success waves-effect waves-light w-100"
              >
                <i class="ri-map-pin-line"></i>
                Masuk
              </router-link>
              <router-link
                v-else-if="
                  jadwal?.status === JADWAL_STATUS.PROGRESS && jenis === 'DINAS'
                "
                :to="{ name: 'PresensiMain' }"
                class="btn btn-danger waves-effect waves-light w-100"
              >
                <i class="ri-map-pin-line"></i>
                Pulang
              </router-link>
              <div
                v-else-if="jadwal?.status === JADWAL_STATUS.TIDAK_HADIR"
                class="badge badge-gradient-success w-100"
              >
                <span class="fs-16"> ALPA </span>
              </div>
            </div>
          </div>
        </BCol>
        <BCol class="my-auto text-center">
          <div>
            <h5 class="pb-1 d-inline-block">
              <span v-if="jadwal?.libur === 0">
                <template v-if="jadwal?.status !== null">
                  {{ jadwal?.presensi?.status_cast }}
                </template>
                <template v-else> BELUM ABSEN </template>
              </span>
              <span
                v-else-if="
                  jadwal?.libur === 1 && jadwal?.status === JADWAL_STATUS.IZIN
                "
              >
                IZIN
              </span>
              <span v-else-if="jadwal?.libur === 1"> LIBUR </span>
            </h5>
          </div>
        </BCol>
      </BRow>
    </div>
  </BCard>
</template>
<script>
import { JADWAL_STATUS } from "@/helpers/utils";

export default {
  props: ["jadwal", "jenis"],
  data() {
    return {
      JADWAL_STATUS,
      IZIN: 4,
      TUKAR_OFF: 6,
    };
  },
};
</script>
