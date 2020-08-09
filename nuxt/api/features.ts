import { NuxtAxiosInstance } from '@nuxtjs/axios';

export type FeatureMap = {
  [name in Feature]?: boolean;
};

export enum Feature {
  PublicUserRegistration = 'registration.public',
  SocialAuthentication = 'auth.social',
}

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
