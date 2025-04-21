import { http } from "@/config";

class LemburService {
  constructor(http) {
    this.http = http;
  }

  async all(params) {
    try {
      const { data } = await this.http.get(`/lembur?${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async selesai() {
    try {
      const { data } = await this.http.get(`/lembur/selesai/by-nip`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async store(form, params) {
    try {
      const { data } = await this.http.post(`/lembur`, form, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async progressByNip() {
    try {
      const { data } = await this.http.get(`/lembur/progress/by-nip`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async confirmList() {
    try {
      const { data } = await this.http.get(`/lembur/confirm/by-nip`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async accSubmit(form) {
    try {
      const { data } = await this.http.put(`/lembur/acc/submit`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async destroy(id) {
    try {
      const { data } = await this.http.delete(`/lembur/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async tolak(form, id) {
    try {
      const { data } = await this.http.put(`/lembur/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async absen(params) {
    try {
      const { data } = await this.http.post(`/lembur-absen`, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async absenPhoto(form, params) {
    try {
      const { data } = await this.http.post(
        `/lembur-absen-photo`,
        form,
        params
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async updateStatus(params) {
    try {
      const { data } = await this.http.put(
        `/lembur-update/status/${params.id}`, params
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async needApproval() {
    try {
      const { data } = await this.http.get(
        `/lembur-count/need-approval`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const lemburService = new LemburService(http);
