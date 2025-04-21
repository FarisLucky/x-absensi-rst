import { http } from "@/config";

class MKaryawanService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/m-karyawan?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data(search) {
    try {
      let q = search !== "" ? `?q=${search}` : "";
      const { data } = await this.http.get(`/m-karyawan/all/data${q}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/m-karyawan/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(nip) {
    try {
      const { data } = await this.http.get(`/m-karyawan/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async update(form, id) {
    try {
      const { data } = await this.http.put(`/m-karyawan/${id}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/m-karyawan/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async jenis() {
    try {
      const { data } = await this.http.get(`/jenis/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async createUser(form) {
    try {
      const { data } = await this.http.post(`/m-karyawan/create-user`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async karyawanByUnit(idUnit) {
    try {
      const { data } = await this.http.get(`/m-karyawan/by-unit/${idUnit}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async updateCuti(params) {
    try {
      const { data } = await this.http.put(
        `/m-karyawan/update-cuti/${params.nip}`,
        params
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async updateSession(nip) {
    try {
      const { data } = await this.http.get(`/m-karyawan/update-session/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async detail(nip) {
    try {
      const { data } = await this.http.get(`/m-karyawan/detail/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async removeDetail(id) {
    try {
      const { data } = await this.http.delete(
        `/m-karyawan/remove-detail/${id}`
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async storeDetail(form) {
    try {
      const { data } = await this.http.post(`/m-karyawan/detail`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async addDetail(form) {
    try {
      const { data } = await this.http.post(`/m-karyawan-detail`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async destroyDetail(id) {
    try {
      const { data } = await this.http.post(`/m-karyawan-detail/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async resign(nip, form) {
    try {
      const { data } = await this.http.put(`/m-karyawan/resign/${nip}`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async searchKaryawanUnit(search) {
    try {
      let q = search !== "" ? `?q=${search}` : "";
      const { data } = await this.http.get(`/m-karyawan-unit${q}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async getReqDevices() {
    try {
      const { data } = await this.http.get(`/m-karyawan-device/req`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async updateDevice(req, id) {
    try {
      const { data } = await this.http.put(`/m-karyawan-device/req/${id}`, req);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async resetDevice(req) {
    try {
      const { data } = await this.http.post(`/m-karyawan-device/reset`, req);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async destoryReqDevice(id) {
    try {
      const { data } = await this.http.delete(`/m-karyawan-device/req/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async sipIndex(query) {
    try {
      const { data } = await this.http.get(`/m-karyawan-detail?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async getNamaKaryawan() {
    try {
      const { data } = await this.http.get(`/m-karyawan-nama`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async sisaCuti() {
    try {
      const { data } = await this.http.get(`/m-karyawan/sisa/cuti`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const mKaryawanService = new MKaryawanService(http);
