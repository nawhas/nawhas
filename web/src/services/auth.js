/* eslint-disable @typescript-eslint/camelcase */
import QueryString from 'query-string';
import { API_DOMAIN, APP_DOMAIN, CLIENT_ID } from '../config';

export function getLoginUrl() {
  const params = {
    client_id: CLIENT_ID,
    response_type: 'token',
    scope: '',
    redirect_url: encodeURIComponent(`${APP_DOMAIN}/auth/callback`),
  };
  return `${API_DOMAIN}/oauth/authorize?${QueryString.stringify(params)}`;
}

export function getSignupUrl()
  const params = { redirect: getLoginUrl() };
  return `${API_DOMAIN}/register?${QueryString.stringify(params)}`;
}

export function getParameterByName(name) {
  const match = new RegExp(`[#&]${name}=([^&]*)`).exec(window.location.hash);
  return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

export function setAccessToken(token, expiration) {
  localStorage.setItem('token', token);
  localStorage.setItem('token_expiration', (Date.now() / 1000) + expiration);
}

export function getAccessToken() {
  return localStorage.getItem('token');
}

export function clearAccessToken() {
  localStorage.removeItem('token');
  localStorage.removeItem('token_expiration');
}

export default {
  getAccessToken,
  getLoginUrl,
  getSignupUrl,
  getParameterByName,
  setAccessToken,
  clearAccessToken,
};
