import { http } from "@/config";

class OnProgressService {
  constructor(http) {
    this.http = http;
  }

  async all(query) {
    try {
      const { data } = await this.http.get(`/on-progress?${query}`);

      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}

export const onProgressService = new OnProgressService(http);
