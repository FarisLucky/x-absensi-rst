import { http } from "@/config";

class MIzinService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-izin/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/m-izin/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-izin/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async show(id) {
    try {
      const { data } = await this.http.get(`/m-izin/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-izin/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-izin/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mIzinService = new MIzinService(http);
