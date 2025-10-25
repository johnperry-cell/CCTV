import type { AxiosResponse } from "axios";
import { pageMaps } from "@/composables/pagination";

export type SearcherOptions = {
  api: string;
  limit?: number;
  method?: "get" | "post";
  appendToUrl?: boolean;
  signal?: AbortSignal;
  baseUrl?: string;
  type?: (typeof pageMaps)[number]["name"];
  onSearch?: (response: AxiosResponse) => void;
};

export type SearchParams = { [key: string]: any };

export interface SearchPagination {
  page?: number;
  limit?: number;
  total?: number;
  cursor?: string;
  nextCursor?: string;
}
