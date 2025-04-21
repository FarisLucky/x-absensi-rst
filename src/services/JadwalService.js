import { http } from "@/config";

class JadwalService {
  constructor(http) {
    this.http = http;
  }

  async all({ bawahan, unit }) {
    try {
      let q = bawahan === "" ? "" : `?unit=${unit}`;

      const { data } = await this.http.get(`/jadwal${q}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async data() {
    try {
      const { data } = await this.http.get(`/jadwal/all/data`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async store(form) {
    try {
      const { data } = await this.http.post(`/jadwal`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async storeWithRange(jadwal) {
    try {
      const { data } = await this.http.post(`/jadwal-all-range`, jadwal);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async storeAll(jadwal) {
    try {
      const { data } = await this.http.post(`/jadwal-all`, jadwal);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async show(params) {
    try {
      const { data } = await this.http.get(`/jadwal-show/${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async showByDate(date) {
    try {
      const { data } = await this.http.get(`/jadwal-show-by-date/${date}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async showByNip({ nip, month, year }) {
    try {
      let query = "";
      if (year !== undefined) {
        query += `?year=${year}`;
      }
      if (month !== undefined) {
        query += `&month=${month}`;
      }
      const { data } = await this.http.get(`/jadwal/${nip}/nip${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async jadwalku(params) {
    try {
      const { data } = await this.http.get(`/jadwalku?${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async jadwalkuHarian() {
    try {
      const { data } = await this.http.get(`/jadwalku-harian`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async updatePresensi(form, id) {
    try {
      const { data } = await this.http.put(
        `jadwal/${id}/update-presensi`,
        form
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async updateJadwalku(kode_shift, id) {
    try {
      const { data } = await this.http.put(`update/jadwalku/${id}`, {
        kode_shift: kode_shift,
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async updateLibur(form, id) {
    try {
      const { data } = await this.http.put(`/jadwal/${id}?column=libur`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async updateShift(form, id) {
    try {
      const { data } = await this.http.put(`/jadwal/${id}?column=shift`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async delete(id) {
    try {
      const { data } = await this.http.delete(`/jadwal/${id}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async deleteAll(ids) {
    try {
      const { data } = await this.http.delete(`/jadwal/all`, { id: ids });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async importExcel(params) {
    try {
      let year = params.get("year");
      let month = params.get("month");
      let file = params.get("file");
      const { data } = await this.http.post(
        `/jadwal/import?year=${year}&month=${month}`,
        { file: file },
        {
          headers: {
            "Content-type": "multipart/form-data",
          },
        }
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async templateExcel() {
    try {
      const { data } = await this.http.get(
        `/jadwal/template/unit?jenis=excel`,
        {
          headers: {
            responseType: "blob",
          },
        }
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async showJadwalUnit(query) {
    try {
      const { data } = await this.http.get(`/jadwal-unit?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async jadwalUnitStatus(query) {
    try {
      const { data } = await this.http.get(`/jadwal-unit-status?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async approval(params) {
    try {
      const { data } = await this.http.post(`/jadwal/approval`, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async kosongkanJadwal(params) {
    try {
      const { data } = await this.http.post(
        `/kosongkan-jadwal?${params.query}`,
        { list_nip: params.list_nip }
      );

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async downloadTemplate(params) {
    try {
      const { data } = await this.http.get(`/jadwal-template?${params}`, {
        responseType: "blob", // Important for handling binary data
      });

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async checkPeriode(params) {
    try {
      const { data } = await this.http.get(`/jadwal/check-periode?${params}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const jadwalService = new JadwalService(http);
