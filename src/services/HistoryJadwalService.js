import { http } from "@/config";

class HistoryJadwalService {
  constructor(http) {
    this.http = http;
  }

  async getJadwal(query) {
    try {
      const { data } = await this.http.get(`/history-jadwal?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async approvalIdx(query) {
    try {
      const { data } = await this.http.get(`/approval?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(id, form) {
    try {
      const { data } = await this.http.put(
        `/history-jadwal/update/${id}`,
        form
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const historyJadwalService = new HistoryJadwalService(http);
