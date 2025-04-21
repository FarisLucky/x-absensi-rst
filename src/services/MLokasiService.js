import { http } from "@/config";

class MLokasiService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-lokasi/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data(status) {
    try {
      status = status === "" ? "" : `?status=${status}`;
      const { data } = await this.http.get(`/m-lokasi/all/data${status}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-lokasi/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async show(id) {
    try {
      const { data } = await this.http.get(`/m-lokasi/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-lokasi/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-lokasi/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mLokasiService = new MLokasiService(http);
