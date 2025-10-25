import type { HasKey, Prettify } from "../utils";
import type { PermissionItem } from "./permission";

export type RoleItem = Prettify<
  HasKey &
    PermissionItem & {
      level: number;
      color: string;
      permissions: Array<PermissionItem>;
    }
>;
