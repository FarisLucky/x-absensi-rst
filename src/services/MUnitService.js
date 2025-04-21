import { http } from "@/config";

class MUnitService {
  constructor(http) {
    this.http = http;
  }

  async all() {
    try {
      const { data } = await this.http.get(`/m-unit/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/m-unit/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-unit/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async storeOrUpdate(form) {
    try {
      const { data } = await this.http.post(`/m-unit/store-or-update`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/m-unit/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-unit/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-unit/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mUnitService = new MUnitService(http);
