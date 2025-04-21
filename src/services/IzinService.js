import { http } from "@/config";

class IzinService {
  constructor(http) {
    this.http = http;
  }

  async batal(id, params) {
    try {
      const { data } = await this.http.put(`/izin/batal/${id}`, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async selesai(query) {
    try {
      const { data } = await this.http.get(`/izin/selesai/by-nip?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async progressByNip() {
    try {
      const { data } = await this.http.get(`/izin/progress/by-nip`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(id) {
    try {
      const { data } = await this.http.get(`/izin/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async confirmIzin() {
    try {
      const { data } = await this.http.get(`/izin/confirm/by-nip`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form, params) {
    try {
      const { data } = await this.http.post(`/izin/`, form, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async accSubmit(form) {
    try {
      const { data } = await this.http.put(`/izin/acc/submit`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async tolakIzin(form) {
    try {
      const { data } = await this.http.put(`/izin/tolak/submit`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/izin/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async tolak(params) {
    try {
      const { data } = await this.http.put(`/tolak/form/${params.id}`, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getLastIzin(nip) {
    try {
      const { data } = await this.http.get(`/izin-last/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async needApproval() {
    try {
      const { data } = await this.http.get(`/izin-count/need-approval`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const izinService = new IzinService(http);
