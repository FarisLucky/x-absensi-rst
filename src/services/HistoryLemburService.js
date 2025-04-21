import { http } from "@/config";

class HistoryLemburService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/history-lembur?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/lembur`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/lembur/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const historyLemburService = new HistoryLemburService(http);
