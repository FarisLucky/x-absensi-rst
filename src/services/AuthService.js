import { http } from "@/config";

class AuthService {
  constructor(http) {
    this.http = http;
  }

  async login(form, headers) {
    try {
      const { data } = await this.http.post(`/login/`, form, headers);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async loginOne(form, headers) {
    try {
      const { data } = await this.http.post(`/login-one/`, form, headers);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async logout() {
    try {
      const { data } = await this.http.delete(`/logout/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async logoutOne() {
    try {
      const { data } = await this.http.delete(`/logout-one/`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async changePassword(form) {
    try {
      const { data } = await this.http.put(`/change-password/`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
  async pengajuan(form) {
    try {
      const { data } = await this.http.put(`/login/pengajuan-web`, form);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const authService = new AuthService(http);
