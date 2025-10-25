import type { HasKey } from "@/types";
import { accessor, safeNum, isWrapped, getPath } from "./utils";
import type { PaginationPlugin } from "@/types/composables/usePagination";

const normalPagination = (): PaginationPlugin => {
  const paths = {
    limit: { paths: ["per_page", "meta.per_page"], exists: true },
    page: { paths: ["current_page", "meta.current_page"], exists: true },
    total: { paths: ["total", "meta.total"], exists: true },
    pages: { paths: ["last_page", "meta.last_page"], exists: true },
  };

  const getPageInfo = (data: HasKey) => {
    return {
      page: safeNum(accessor(data, getPath(paths, data, "page")!)),
      limit: safeNum(accessor(data, getPath(paths, data, "limit")!)),
      total: safeNum(accessor(data, getPath(paths, data, "total")!)),
      pages: safeNum(accessor(data, getPath(paths, data, "pages")!)),
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

  const parseFirst = (data: HasKey) => {
    const { page } = getPageInfo(data);
    return {
      get page() {
        return page && page > 1 ? 1 : undefined;
      },
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
    name: "Normal",
    checks: paths,
    toWatch: ["page"],
    toAppend: { include: ["page"] },
    defaults: {
      page: 1,
      total: 0,
    },
    parsers: {
      first: parseFirst,
      next: parseNext,
      prev: parsePrev,
      last: parseLast,
      jump: jumpPage,
      limit: (data: HasKey) => getPageInfo(data).limit,

      current: {
        page: (data: HasKey) => getPageInfo(data).page,
        total: (data: HasKey) => getPageInfo(data).total,
        pages: (data: HasKey) => getPageInfo(data).pages,
      },
    },
    isWrapped,
  };
};

export default normalPagination();
