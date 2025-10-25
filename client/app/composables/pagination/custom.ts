import type { HasKey } from "@/types";
import { accessor, safeNum, getPath } from "./utils";
import type {
  PaginationPlugin,
  PaginationPluginPaths,
} from "@/types/composables/usePagination";

const mitdPagination = (): PaginationPlugin => {
  const paths = {
    limit: { paths: ["limit"], exists: true },
    page: { paths: ["page"], exists: true },
    total: { paths: ["total"], exists: true },
    pages: { paths: ["pages"], exists: true },
  } as PaginationPluginPaths;

  const getPageInfo = (data: HasKey) => {
    return {
      page: safeNum(accessor(data, getPath(paths, data, "page")!)),
      limit: safeNum(accessor(data, getPath(paths, data, "limit")!)),
      total: safeNum(accessor(data, getPath(paths, data, "total")!)),
      pages: safeNum(accessor(data, getPath(paths, data, "pages")!)),
    };
  };

  const parseLast = (data: HasKey) => {
    const { page, limit, total } = getPageInfo(data);
    const pages = limit && total ? Math.ceil(total / limit) : 1;
    return {
      get page() {
        return page && pages > page ? pages : undefined;
      },
    };
  };

  const parseNext = (data: HasKey) => {
    const { page, limit, total } = getPageInfo(data);

    const pages = limit && total ? Math.ceil(total / limit) : 1;
    return {
      get page() {
        return page && pages > page ? page + 1 : undefined;
      },
    };
  };

  const parsePrev = (data: HasKey) => {
    const { page } = getPageInfo(data);

    return {
      get page() {
        return page && page > 1 ? page - 1 : undefined;
      },
    };
  };

  const jumpPage = (data: HasKey, page: number | string) => {
    const { page: current_page, pages } = getPageInfo(data);
    const p = safeNum(page);
    return {
      get page() {
        let result = current_page;
        if (p && pages) {
          if (p < 1) result = 1;
          if (p > pages) result = pages;
        }
        return result;
      },
    };
  };

  return {
    name: "Custom",
    checks: paths,
    toWatch: ["page"],
    toAppend: { exclude: ["total", "pages"] },
    defaults: {
      page: 1,
      total: 0,
      pages: 0,
    },
    parsers: {
      first: (_: HasKey) => ({
        get page() {
          return 1;
        },
      }),
      last: parseLast,
      next: parseNext,
      prev: parsePrev,
      jump: jumpPage,

      current: {
        page: (data: HasKey) => getPageInfo(data).page,
        total: (data: HasKey) => getPageInfo(data).total,
        pages: (data: HasKey) => getPageInfo(data).pages,
      },

      limit: (data: HasKey) => getPageInfo(data).limit || 0,
    },
  };
};

export default mitdPagination();
