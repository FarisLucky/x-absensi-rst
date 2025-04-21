import { http } from "@/config";

class DashboardService {
  constructor(http) {
    this.http = http;
  }

  async progress() {
    try {
      const { data } = await this.http.get(`/dashboard/progress-presensi`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async fcm() {
    try {
      const { data } = await this.http.get(`/dashboard/fcm`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async presensiHarian() {
    try {
      const { data } = await this.http.get(`/dashboard/presensi/harian`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async tablePresensiHarian(query) {
    try {
      const { data } = await this.http.get(
        `/dashboard/table-presensi/harian?${query}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async tableIzinHarian() {
    try {
      const { data } = await this.http.get(`/dashboard/table-izin/harian`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async jadwal() {
    try {
      const { data } = await this.http.get(`/dashboard/jadwal/harian`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async statistikKehadiran(params) {
    try {
      const { data } = await this.http.get(
        `/dashboard/statistik-hadir?${params}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async statistikKehadiranDivisi(params) {
    try {
      const { data } = await this.http.get(
        `/dashboard/statistik-divisi?${params}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async statistikKehadiranDistr(params) {
    try {
      const { data } = await this.http.get(
        `/dashboard/statistik-distr?${params}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async statistikKehadiranTrend() {
    try {
      const { data } = await this.http.get(`/dashboard/statistik-trend`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async statistikGender() {
    try {
      const { data } = await this.http.get(`/dashboard/statistik-gender`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async listPresensiTerendah(params) {
    try {
      const { data } = await this.http.get(
        `/dashboard/list-presensi?${params}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const dashboardService = new DashboardService(http);
