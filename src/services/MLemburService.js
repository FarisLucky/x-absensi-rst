import { http } from "@/config";

class MLemburService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-lembur/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data(status) {
    try {
      status = status === "" ? "" : `?status=${status}`;
      const { data } = await this.http.get(`/m-lembur/all/data${status}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-lembur/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async show(id) {
    try {
      const { data } = await this.http.get(`/m-lembur/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-lembur/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-lembur/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mLemburService = new MLemburService(http);
