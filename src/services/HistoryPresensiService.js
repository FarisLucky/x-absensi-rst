import { http } from "@/config";

class HistoryPresensiService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/history-presensi?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async progress(query) {
    try {
      const { data } = await this.http.get(
        `/history-presensi-progress?${query}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async grafikKehadiran(query) {
    try {
      const { data } = await this.http.get(`/history-presensi-grafik?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async searchByKaryawan(params) {
    try {
      const { data } = await this.http.get(
        `/history-presensi/search/karyawan?${params}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const historyPresensiService = new HistoryPresensiService(http);
