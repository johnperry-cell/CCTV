import type { HasKey, Prettify } from "../utils";

export type PermissionItem = Prettify<
  HasKey & {
    id: string;
    name: string;
    description: string;
    date: string;
  }
>;
