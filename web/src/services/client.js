import axios from 'axios';
import { API_DOMAIN } from '../config';

class Client {
  constructor() {
    this.base = API_DOMAIN;
  }

  get(path, params = {}) {
    return this.request('GET', path, params);
  }

  post(path, data = {}) {
    return this.request('POST', path, data);
  }

  put(path, data = {}) {
    return this.request('PUT', path, data);
  }

  patch(path, data = {}) {
    return this.request('PATCH', path, data);
  }

  delete(path, data = {}) {
    return this.request('DELETE', path, data);
  }

  request(method = 'GET', path, data = {}) {
    const config = { method };
    let url = path;

    if (url.startsWith('/')) {
      url = url.substr(1);
    }

    config.url = `${this.base}/${url}`;

    if (method === 'GET') {
      config.params = data;
    } else {
      config.data = data;
    }

    config.headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    };

    return axios(config);
  }
}

export default new Client();
