import { IncomingMessage } from 'http';
import { AxiosRequestConfig } from 'axios';
import { NuxtAxiosInstance } from '@nuxtjs/axios';
import { User } from '@/entities/user';

/*
 * Request Payloads
 */
export interface LoginPayload {
  email: string;
  password: string;
}

export interface RegisterPayload {
  email: string;
  password: string;
  name: string;
}

export class AuthApi {
  constructor(
    private axios: NuxtAxiosInstance,
    private request?: IncomingMessage,
  ) {}

  async login(payload: LoginPayload): Promise<User> {
    return await this.axios.$post<User>('v1/auth/login', payload);
  }

  async register(payload: RegisterPayload): Promise<User> {
    return await this.axios.$post<User>('v1/auth/register', payload);
  }

  async user(): Promise<User> {
    const options: AxiosRequestConfig = process.server ? {
      headers: {
        referer: this.request?.headers.host,
      },
    } : {};

    return await this.axios.$get<User>('v1/auth/user', options);
  };

  async logout(): Promise<void> {
    await this.axios.$post('v1/auth/logout');
  }
}
