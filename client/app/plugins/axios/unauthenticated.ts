import type { AxiosInstance, AxiosResponse } from "axios";

export default (api: AxiosInstance) => {
  api.interceptors.response.use(
    (response: AxiosResponse) => Promise.resolve(response),
    (error: any) => {
      const { $router } = useNuxtApp();
      if (
        error.response?.status === 401 &&
        $router.currentRoute.value.meta?.requiresAuth
      ) {
        const $auth = useAuthStore();
        if ($auth.isLoggedIn) {
          $auth.reset();
        }

        if ($router.currentRoute.value.name !== "login") {
          $router.push({
            name: "login",
            query: { redirect: $router.currentRoute.value.fullPath },
          });
        }
      }
      return Promise.reject(error);
    },
  );
};
