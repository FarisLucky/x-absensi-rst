import { http } from "@/config";

class MKaryawanDokService {
  constructor(http) {
    this.http = http;
  }

  async all(params) {
    try {
      const { data } = await this.http.get(`/m-karyawan-dok?${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/m-karyawan-dok/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-karyawan-dok/`, form, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async storeOrUpdate(form) {
    try {
      const { data } = await this.http.post(
        `/m-karyawan-dok/store-or-update`,
        form
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/m-karyawan-dok/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-karyawan-dok/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-karyawan-dok/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mKaryawanDokService = new MKaryawanDokService(http);
