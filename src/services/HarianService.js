import { http } from "@/config";

class HarianService {
  constructor(http) {
    this.http = http;
  }

  async kerja(params) {
    try {
      const { data } = await this.http.get(`/harian-kerja?${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const harianService = new HarianService(http);
