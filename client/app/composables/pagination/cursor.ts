import type { HasKey } from "@/types";
import { accessor, isset, isWrapped, safeNum, getPath } from "./utils";
import type {
  PaginationPlugin,
  PaginationPluginPaths,
} from "@/types/composables/usePagination";

const cursorPagination = (): PaginationPlugin => {
  const paths = {
    next_cursor: { paths: ["next_cursor", "meta.next_cursor"], exists: true },
    prev_cursor: { paths: ["prev_cursor", "meta.prev_cursor"], exists: true },
    next_page_url: { paths: ["next_page_url", "links.next"], exists: true },
    prev_page_url: { paths: ["prev_page_url", "links.prev"], exists: true },
    limit: { paths: ["per_page", "meta.per_page"], exists: true },
  } as PaginationPluginPaths;

  const getPageInfo = (data: HasKey) => {
    return {
      limit: safeNum(accessor(data, getPath(paths, data, "limit")!)),
      next_cursor: accessor(data, getPath(paths, data, "next_cursor")!),
      prev_cursor: accessor(data, getPath(paths, data, "prev_cursor")!),
    };
  };

  const parseNext = (data: HasKey) => {
    const { next_cursor } = getPageInfo(data);
    return {
      get cursor() {
        return next_cursor;
      },
    };
  };

  const parsePrev = (data: HasKey) => {
    const { prev_cursor } = getPageInfo(data);
    return {
      get cursor() {
        return prev_cursor;
      },
    };
  };

  return {
    name: "Cursor",
    checks: paths,
    toWatch: ["cursor"],
    toAppend: { include: ["cursor"] },
    defaults: { cursor: null },
    parsers: {
      next: parseNext,
      prev: parsePrev,
      limit: (data: HasKey) => getPageInfo(data).limit,
    },
    isWrapped,
  };
};

export default cursorPagination();
