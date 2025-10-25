import type { HasKey, Prettify } from "../utils";
import type { PermissionItem } from "./permission";

export type Client = Prettify<
  HasKey & {
    client_id?: string;
    client_name: string;
    client_domain: string;
    client_secret?: string;
    active: boolean;
    permissions: Array<PermissionItem>;
  }
>;
