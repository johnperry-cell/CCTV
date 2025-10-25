import type { RouteLocationRaw } from "vue-router";
import type { Prettify } from "./utils";

export type AvatarOptions = {
  label?: string;
  icon?: string;
  divider?: boolean;
  to?: RouteLocationRaw;
  hidden?: boolean | ComputedRef<boolean>;
  action?: () => void;
};

export type MenuOption = Prettify<
  AvatarOptions & {
    active?: boolean;
    exact?: boolean;
    children?: Array<MenuOption>;
  }
>;
