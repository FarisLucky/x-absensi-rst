import { http } from "@/config";

class HistoryIzinService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/history-izin?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const historyIzinService = new HistoryIzinService(http);
