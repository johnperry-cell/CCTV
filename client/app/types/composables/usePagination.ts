import type { HasKey } from "../utils";

export type MapInfo = { paths: Array<string>; exists: boolean };

export type PaginationPlugin = {
  name: string;
  checks: Record<string, MapInfo>;
  toWatch?: Array<string>;
  toAppend?: { include?: Array<string>; exclude?: Array<string> };

  defaults: Record<string, any>;

  parsers: {
    current?: Record<string, (data: HasKey) => any>;
    first?: (data: HasKey) => any;
    next?: (data: HasKey) => any;
    prev?: (data: HasKey) => any;
    last?: (data: HasKey) => any;
    jump?: (data: HasKey, page: number | string) => any;

    limit: (data: HasKey) => number | undefined;
  };

  isWrapped?: (data: HasKey) => boolean;
};

export type PaginationPluginPaths = Record<
  string,
  {
    paths: Array<string>;
    exists: boolean;
  }
>;

export type PaginationMeta = {
  next?: HasKey;
  prev?: HasKey;
  first?: HasKey;
  last?: HasKey;
  jump?: Function;
  wrapped: boolean;

  watch?: Array<string>;
  append?: { include?: Array<string>; exclude?: Array<string> };
};

export type PaginationAbilities = {
  next: boolean;
  prev: boolean;
  first: boolean;
  last: boolean;
  jump: boolean;
};
