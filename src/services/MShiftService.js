import { http } from "@/config";

class MShiftService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-shift/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/m-shift/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-shift/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async show(id) {
    try {
      const { data } = await this.http.get(`/m-shift/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-shift/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-shift/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mShiftService = new MShiftService(http);
