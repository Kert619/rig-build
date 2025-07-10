import { defineBoot } from '#q-app/wrappers';
import axios, { type AxiosInstance } from 'axios';

declare module 'vue' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance;
    $api: AxiosInstance;
  }
}

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({
  baseURL: process.env.API + '/api',
  withCredentials: true,
  withXSRFToken: true,
});
const web = axios.create({
  baseURL: process.env.API ?? '',
  withCredentials: true,
  withXSRFToken: true,
});

api.interceptors.response.use(null, function (error) {
  const path = window.location.pathname;

  if (error.response.status === 401 && path != '/auth/login') {
    window.location.href = '/auth/login';
  }
  return Promise.reject(new Error('Unauthenticated'));
});

export default defineBoot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios;
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api;
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
});

export { api, web };
