import axios from "axios";

import AbortControllers from "./axios/useAbortControllers";
import Unauthenticated from "./axios/unauthenticated";
import Notify from "./axios/notify";
import Csrf from "./axios/csrf";

export default defineNuxtPlugin(async ({ $config }) => {
  const apiConfig = computed(() => ({
    withCredentials: true,
    withXSRFToken: true,
    headers: {
      Accept: "application/json",
      Authorization: null,
    },
    baseURL: $config.public.baseUrl,
  }));

  const api = axios.create(apiConfig.value);

  const { getCsrfToken } = Csrf(api);
  AbortControllers(api);
  Unauthenticated(api);
  const { exclude } = Notify(api);

  await getCsrfToken(api).catch((e) => e);

  return {
    provide: {
      api,
      apiConfig: toValue(apiConfig),
      apiExcludeFromNotif: exclude,
    },
  };
});
