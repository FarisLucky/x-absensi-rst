import { http } from "@/config";

class RekapService {
  constructor(http) {
    this.http = http;
  }

  async harian(query) {
    try {
      const { data } = await this.http.get(`/rekap/presensi/excel?${query}`, {
        responseType: "blob", // Important for handling binary data
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async presensiBulanan(query) {
    try {
      const { data } = await this.http.get(`/rekap-bulanan/export?${query}`, {
        responseType: "blob", // Important for handling binary data
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async kehadiran(query) {
    try {
      const { data } = await this.http.get(`/rekap-kehadiran?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const rekapService = new RekapService(http);
