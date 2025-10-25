import normal from "./normal";
import simple from "./simple";
import cursor from "./cursor";
import custom from "./custom";

import { is } from "./utils";

import type { HasKey } from "@/types";
import type {
  PaginationPlugin,
  PaginationMeta,
  PaginationAbilities,
} from "@/types/composables/usePagination";

export * as utils from "./utils";
export const pageMaps = {
  custom,
  normal,
  simple,
  cursor,
} as Record<string, PaginationPlugin>;

export const usePagination = (mapType?: (typeof pageMaps)[number]["name"]) => {
  const pagination = ref<Record<string, any>>({ limit: 10 });

  const meta = ref<PaginationMeta>({ wrapped: false });

  const abilities = ref<PaginationAbilities>({
    next: false,
    prev: false,
    first: false,
    last: false,
    jump: false,
  });

  const getMap = (map: (typeof pageMaps)[number]["name"]): PaginationPlugin => {
    return Object.values(pageMaps).find((m) => {
      return m.name.toLowerCase() === map.toLowerCase();
    })!;
  };

  const findMap = <T extends HasKey>(data: T) => {
    return Object.values(pageMaps).find((m) => is(data, m.checks));
  };

  const parse = <T extends HasKey>(data: T) => {
    const map = mapType ? getMap(mapType) : findMap(data);

    if (map) {
      if (mapType && !is(data, map.checks)) {
        warn(
          `Pagination method "${mapType}" is not compatible with the response data.`,
        );
      }

      setMeta(map, data);
      setAbilities(meta.value);

      pagination.value.limit = map.parsers.limit(data) || 0;

      if (map.parsers.current) {
        Object.keys(map.parsers.current).forEach((key) => {
          Object.assign(pagination.value, {
            [key]: map.parsers.current![key](data),
          });
        });
      }
    } else {
      error("UNKOWN PAGINATION METHOD!");
    }
  };

  const setAbilities = (meta: PaginationMeta): PaginationAbilities => {
    abilities.value.next =
      !!meta.next && Object.values(meta.next).some((v) => !!v);
    abilities.value.prev =
      !!meta.prev && Object.values(meta.prev).some((v) => !!v);
    abilities.value.first =
      !!meta.first && Object.values(meta.first).some((v) => !!v);
    abilities.value.last =
      !!meta.last && Object.values(meta.last).some((v) => !!v);
    abilities.value.jump = !!meta.jump;
    return abilities.value;
  };

  const setMeta = (map: PaginationPlugin, data: HasKey): PaginationMeta => {
    meta.value.next = map.parsers.next?.(data);
    meta.value.prev = map.parsers.prev?.(data);
    meta.value.first = map.parsers.first?.(data);
    meta.value.last = map.parsers.last?.(data);
    meta.value.jump = map.parsers.jump
      ? (page: number | string) => map.parsers.jump?.(data, page)
      : undefined;
    meta.value.wrapped = map.isWrapped?.(data) || false;
    return meta.value;
  };

  if (mapType && getMap(mapType)) {
    const m = getMap(mapType);
    Object.assign(pagination.value, m?.defaults);
    meta.value.append = m.toAppend;
    meta.value.watch = m.toWatch ?? [];
  }

  return { pagination, abilities, meta, parse };
};
