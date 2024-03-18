import type { NuxtAxiosInstance } from '@nuxtjs/axios';
import { FeatureMap } from '@/entities/feature';

export interface FeaturesIndexResponse {
  data: FeatureMap;
}

export class FeaturesApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async index(): Promise<FeatureMap> {
    const response = await this.axios.$get<FeaturesIndexResponse>('v1/features');
    return response.data;
  }
}
