import { http } from "@/config";

class PresensiService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/presensi/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/presensi/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/presensi/show/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/presensi/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(id, form) {
    try {
      const { data } = await this.http.put(`/presensi/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async masuk(form) {
    try {
      const { data } = await this.http.post(`/presensi/masuk`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async pulang(form) {
    try {
      const { data } = await this.http.post(`/presensi/pulang`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async jadwalByNip() {
    try {
      const { data } = await this.http.get(`/presensi/jadwal-hari-ini`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async checkPresensiMasuk() {
    try {
      const { data } = await this.http.get(`/presensi/check-absen-masuk`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async checkPresensiPulang() {
    try {
      const { data } = await this.http.get(`/presensi/check-absen-pulang`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async radiusValidation(form) {
    try {
      const { data } = await this.http.post(`/presensi/validasi-radius`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async updateStatus(status, id) {
    try {
      const { data } = await this.http.put(`/presensi/${id}/status-update`, {
        status: status,
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/presensi/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async rekapExcel(tgl) {
    try {
      const { data } = await this.http.get(
        `/rekap/presensi/excel?tanggal=${tgl}`,
        {
          responseType: "blob",
        }
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async reverseGeocoding({ lat, lon }) {
    try {
      let apiKey = "a21eb5b2fb3440e78aaab45746f20b8f";

      const { data } = await this.http.get(
        `https://api.geoapify.com/v1/geocode/reverse?lat=${lat}&lon=${lon}&apiKey=${apiKey}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async ketTelat(id, form) {
    try {
      const { data } = await this.http.put(`/presensi/telat/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const presensiService = new PresensiService(http);
