import type { Prettify, HasKey } from "../utils";

export type FlattenOptions = Prettify<
  HasKey & {
    delimiter?: string;
    includeArrays?: boolean;
  }
>;
