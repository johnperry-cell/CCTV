import type { AxiosInstance } from "axios";

export default (api: AxiosInstance) => {
  const retry = ref(0);
  const maxRetries = 5;

  const getCsrfToken = async (api: AxiosInstance) => {
    return new Promise((resolve, reject) => {
      api.get("/csrf-cookie").then(resolve).catch(reject);
    });
  };

  api.interceptors.response.use(
    (response: any) => Promise.resolve(response),
    async (error: any) => {
      if (error.response?.status === 419 && retry.value < maxRetries) {
        retry.value += 1;
        log("REFRESHING CSRF TOKEN");
        await getCsrfToken(api);
        return api(error.config);
      }
      retry.value = 0;
      return Promise.reject(error);
    },
  );

  return { getCsrfToken };
};
