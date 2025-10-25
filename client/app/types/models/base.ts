import type { HasKey, Prettify } from "../utils";

export type BaseModel = Prettify<
  HasKey & {
    id?: string;
  }
>;

export type Common = HasKey &
  Required<BaseModel> & {
    name: string;
  };
