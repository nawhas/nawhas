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
  ) {}

  async login(payload: LoginPayload): Promise<User> {
    await this.axios.get('/sanctum/csrf-cookie');
    return await this.axios.$post<User>('v1/auth/login', payload);
  }

  async register(payload: RegisterPayload): Promise<User> {
    return await this.axios.$post<User>('v1/auth/register', payload);
  }

  async user(): Promise<User> {
    return await this.axios.$get<User>('v1/auth/user');
  };

  async logout(): Promise<void> {
    await this.axios.$post('v1/auth/logout');
  }
}
