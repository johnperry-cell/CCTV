import type { HasKey } from "~/types";
import {
  accessor,
  safeNum,
  isWrapped,
  extractUrlParams,
  getPath,
} from "./utils";
import type { PaginationPlugin } from "~/types/composables/usePagination";

const simplePagination = (): PaginationPlugin => {
  const paths = {
    limit: { paths: ["per_page", "meta.per_page"], exists: true },
    page: { paths: ["current_page", "meta.current_page"], exists: true },
    total: { paths: ["total", "meta.total"], exists: false },
    pages: { paths: ["last_page", "meta.last_page"], exists: false },
    next: { paths: ["next_page_url", "links.next"], exists: true },
    prev: { paths: ["prev_page_url", "links.prev"], exists: true },
  };

  const getPageInfo = (data: HasKey) => {
    return {
      page: safeNum(accessor(data, getPath(paths, data, "page")!)),
      limit: safeNum(accessor(data, getPath(paths, data, "limit")!)),

      next: safeNum(
        extractUrlParams(accessor(data, getPath(paths, data, "next")!)).page,
      ),
      prev: safeNum(
        extractUrlParams(accessor(data, getPath(paths, data, "prev")!)).page,
      ),
    };
  };

  const parseFirst = (data: HasKey) => {
    const { prev } = getPageInfo(data);
    return {
      get page() {
        return prev ? 1 : undefined;
      },
    };
  };

  const parseNext = (data: HasKey) => {
    const { next } = getPageInfo(data);
    return {
      get page() {
        return next;
      },
    };
  };

  const parsePrev = (data: HasKey) => {
    const { prev } = getPageInfo(data);
    return {
      get page() {
        return prev;
      },
    };
  };

  return {
    name: "Simple",
    checks: paths,
    toWatch: ["page"],
    toAppend: { include: ["page"] },
    defaults: {
      page: 1,
    },
    parsers: {
      first: parseFirst,
      next: parseNext,
      prev: parsePrev,
      limit: (data: HasKey) => getPageInfo(data).limit,
      current: {
        page: (data: HasKey) => getPageInfo(data).page,
      },
    },
    isWrapped,
  };
};

export default simplePagination();
