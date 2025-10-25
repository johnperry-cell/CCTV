import type {
  AxiosInstance,
  AxiosResponse,
  InternalAxiosRequestConfig,
} from "axios";

export default (api: AxiosInstance) => {
  const controllers = new Map();
  const { uniqid } = useUtils();

  api.interceptors.request.use(
    async (config: InternalAxiosRequestConfig) => {
      if (!config.signal) {
        const controller = new AbortController();
        config.signal = controller.signal;
        controllers!.set(uniqid("api_call_"), controller);
      }
      return config;
    },
    (error) => Promise.reject(error),
  );

  api.interceptors.response.use(
    (response: AxiosResponse) => Promise.resolve(response),
    async (error) => {
      if (error.response?.status === 401) {
        controllers!.forEach((ctrl, id) => {
          warn("ABORTING", id);
          ctrl.abort();
          controllers!.delete(id);
        });
      }
      return Promise.reject(error);
    },
  );

  return { controllers };
};
