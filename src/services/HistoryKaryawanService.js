import { http } from "@/config";

class HistoryKaryawanService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/history-karyawan?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async kinerjaStaf(query) {
    try {
      const { data } = await this.http.get(
        `/history-karyawan/kinerja?${query}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const historyKaryawanService = new HistoryKaryawanService(http);
