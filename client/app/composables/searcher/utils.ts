import type { SearchParams } from "@/types/composables/useSearcher";

export const _filterParams = <P extends SearchParams>(
  p: P,
  excluded: string[] = [],
  only: string[] = [],
): SearchParams => {
  const excludedSet = new Set(excluded);
  return Object.keys(p)
    .filter((key) => !excludedSet.has(key))
    .filter((key) => only.length === 0 || only.includes(key))
    .filter((key) => !!p[key])
    .reduce((result, key) => {
      result[key] = p[key];
      return result;
    }, {} as SearchParams);
};

export const _paramSort = <T extends SearchParams>(params: T): T => {
  return Object.keys(params)
    .sort()
    .reduce((obj, key) => ({ ...obj, [key]: params[key] }), {} as T);
};
