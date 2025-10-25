import type { AxiosResponse } from "axios";
import defu from "defu";
import type {
  SearcherOptions,
  SearchParams,
} from "@/types/composables/useSearcher";
import { _filterParams, _paramSort } from "./searcher/utils";
import { usePagination } from "./pagination";

export const useSearcher = <T extends SearchParams>(
  options: SearcherOptions | ComputedRef<SearcherOptions>,
) => {
  const $route = useRoute();
  const { $api, $router } = useNuxtApp();
  const { pagination, abilities, meta, parse } = usePagination(
    toValue(options).type ?? "normal",
  );

  const { flattenObject, unflattenObject } = useUtils();

  const _default = {
    limit: 25,
    method: "get",
    appendToUrl: false,
  } as SearcherOptions;

  const _options = computed<SearcherOptions>(() =>
    defu(toValue(options), _default),
  );

  const loading = ref<boolean>(false);
  const searchStates = ref({ preventWatch: false, bounced: false });

  const params = ref<T>(
    Object.assign(
      {},
      pagination.value,
      { limit: _options.value.limit },
      _filterParams(unflattenObject($route.query)),
    ) as T,
  );

  const watcher = computed(() => {
    const w = {};
    meta.value.watch?.forEach((k) => {
      Object.assign(w, { [k]: pagination.value[k] });
    });
    return w;
  });

  const search = (): Promise<AxiosResponse> => {
    return new Promise((resolve, reject) => {
      loading.value = true;
      searchStates.value.preventWatch = true;

      const p = _getParams();

      _appendToRoute(p);
      $api
        .request({
          url: _options.value.api,
          baseURL: _options.value.baseUrl || $api.defaults.baseURL,
          method: _options.value.method,
          signal: _options.value.signal,
          params: p,
        })
        .then((response) => {
          searchStates.value.bounced = false;
          parse(response.data);
          Object.assign(params.value, pagination.value);
          _options.value.onSearch?.(response);
          resolve(response);
        })
        .catch(reject)
        .finally(() => {
          loading.value = false;
          searchStates.value.preventWatch = false;
        });
    });
  };

  const _getParams = () => {
    return _paramSort(
      _filterParams(
        Object.assign({ limit: _options.value.limit }, params.value),
      ),
    );
  };

  const _appendToRoute = (p: SearchParams) => {
    if (_options.value.appendToUrl) {
      const query = {};

      Object.keys(p)
        .filter((key) => !meta.value.append?.exclude?.includes(key))
        .filter((key) => meta.value.append?.include?.includes(key) || true)
        .forEach((key) => {
          Object.assign(query, { [key]: p[key] });
        });

      $router.push({ query: flattenObject(_paramSort(_filterParams(query))) });
    }
  };

  const nextPage = () => {
    if (abilities.value.next) {
      updateParams(meta.value.next);
      debounced();
    }
  };

  const prevPage = () => {
    if (abilities.value.prev) {
      updateParams(meta.value.prev);
      debounced();
    }
  };

  const firstPage = () => {
    if (abilities.value.first) {
      updateParams(meta.value.first);
      debounced();
    }
  };

  const lastPage = () => {
    if (abilities.value.last) {
      updateParams(meta.value.last);
      debounced();
    }
  };

  const changePage = (page: number | string) => {
    if (abilities.value.jump) {
      updateParams(meta.value.jump?.(page));
      debounced();
    }
  };

  const debounced = useDebounceFn(() => {
    if (searchStates.value.bounced) {
      search();
    }
    searchStates.value.bounced = true;
  }, 50);

  const updateParams = (updated: any) => {
    searchStates.value.bounced = true;
    Object.assign(params.value, _filterParams(updated));
  };

  watch(
    watcher,
    (val) => {
      if (!searchStates.value.preventWatch) {
        updateParams(val);
        debounced();
      }
    },
    { deep: true },
  );

  watch(
    () => $route.query,
    (val, old) => {
      const same =
        JSON.stringify(_paramSort(unflattenObject(val))) ===
        JSON.stringify(_paramSort(unflattenObject(old)));
      if (!same && !searchStates.value.preventWatch) {
        updateParams(unflattenObject(val));
        debounced();
      }
    },
    { deep: true },
  );

  return {
    pagination,
    params,
    loading,
    pageCan: abilities,
    search,
    nextPage,
    prevPage,
    firstPage,
    lastPage,
    changePage,
  };
};
