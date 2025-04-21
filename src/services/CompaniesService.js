import { http } from "@/config";

class CompaniesService {
  constructor(http) {
    this.http = http;
  }
  async all() {
    try {
      const { data } = await this.http.get(`/companies`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/companies/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/companies/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const companiesService = new CompaniesService(http);
