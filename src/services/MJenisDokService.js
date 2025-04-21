import { http } from "@/config";

class MJenisDokService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-jenisdok/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/m-jenisdok/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-jenisdok/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/m-jenisdok/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-jenisdok/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-jenisdok/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mJenisDokService = new MJenisDokService(http);
