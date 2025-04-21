import { http } from "@/config";

class NotificationService {
  constructor(http) {
    this.http = http;
  }

  async getJadwal() {
    try {
      const { data } = await this.http.get(`/jadwal-notif`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const notificationService = new NotificationService(http);
