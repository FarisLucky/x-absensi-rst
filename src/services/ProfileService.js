import { http } from "@/config";

class ProfileService {
  constructor(http) {
    this.http = http;
  }

  async myProfil() {
    try {
      const { data } = await this.http.get(`/my-profil`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async show(nip) {
    try {
      const { data } = await this.http.get(`/profil/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async jadwal(query) {
    try {
      const { data } = await this.http.get(`/profil-jadwal?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getEvent(query) {
    try {
      const { data } = await this.http.get(`/profil-jadwal/get-event?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async changePhoto(form, params) {
    try {
      const { data } = await this.http.post(`/profil-change`, form, params);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async statistikPresensi(nip) {
    try {
      const { data } = await this.http.get(`/profil/statistik-presensi/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getPelatihan(nip) {
    try {
      const { data } = await this.http.get(`/profil/pelatihan/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getIzin({ nip, query }) {
    try {
      const { data } = await this.http.get(`/profil/izin/${nip}?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getTukar({ nip, query }) {
    try {
      const { data } = await this.http.get(`/profil/tukar/${nip}?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async getPresensi({ nip, query }) {
    try {
      const { data } = await this.http.get(`/profil/presensi/${nip}?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }

  async grafikStatistik(nip) {
    try {
      const { data } = await this.http.get(`/profil/grafik/statistik/${nip}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const profileService = new ProfileService(http);
