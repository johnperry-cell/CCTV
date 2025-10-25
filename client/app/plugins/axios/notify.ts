import type { AxiosInstance, AxiosResponse } from "axios";

export default (api: AxiosInstance) => {
  const { trimSlashes } = useUtils();
  const exclusions = ref<Array<Record<string, any>>>([
    { code: "ERR_CANCELED" },
  ]);

  const exclude = (error_code: string | number, url?: string) => {
    exclusions.value.push({
      code: error_code,
      url: url,
    });
  };

  const isExcluded = (error: any) => {
    if (error.response?.data?.error_code) {
      return true;
    }

    const index = exclusions.value.findIndex(
      (e) =>
        (e.code == error.code || e.code == error.response?.status) &&
        (e.url ? trimSlashes(e.url) == trimSlashes(error.config.url) : true),
    );

    return index >= 0;
  };

  const getTitle = (error: any) => {
    return error.response?.data?.message ?? error.message;
  };

  const getDescription = (error: any) => {
    return error.response ? error.message : null;
  };

  api.interceptors.response.use(
    (response: AxiosResponse) => Promise.resolve(response),
    (error: any) => {
      if (!isExcluded(error)) {
        const toast = useToast();
        toast.add({
          title: getTitle(error),
          description: getDescription(error),
          color: "error",
          icon: "tabler:exclamation-circle",
        });
      }
      return Promise.reject(error);
    },
  );

  return { exclude };
};
