import type { HasKey } from "~/types";
import type { PaginationPluginPaths } from "~/types/composables/usePagination";

export const accessor = (obj: any, path: string) => {
  return path?.split(".").reduce((a, b) => a?.[b], obj);
};

export const is = <T extends HasKey>(data: T, map: HasKey): boolean => {
  const keys = Object.keys(map) as (keyof typeof map)[];
  return keys.every((key) => {
    const paths = map[key].paths;
    const exists = map[key].exists;
    return paths.some((path: string) => isset(accessor(data, path))) === exists;
  });
};

export const isset = (data: any): boolean => {
  return typeof data !== "undefined";
};

export const safeNum = (data: any): number | undefined => {
  return Number.isNaN(Number(data)) ? undefined : Number(data);
};

export const isWrapped = <T extends HasKey>(data: T): boolean => {
  return Object.hasOwn(data, "meta");
};

export const extractUrlParams = (url?: string) => {
  if (!url) return {};
  const urlParams = new URLSearchParams(url.split("?")[1]);
  return Object.fromEntries(urlParams);
};

export const getPath = <T extends PaginationPluginPaths>(
  paths: T,
  data: HasKey,
  name: keyof T,
) => {
  return paths[name].paths.find((p) => isset(accessor(data, p)));
};
