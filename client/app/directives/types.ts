import type { App } from "vue";
import type { ClassNameValue } from "tailwind-merge";

export type DirectiveFn = (app: App) => void;
export type RippleOptions = {
  class?: ClassNameValue;
  disabled?: boolean;
  tag?: string;
};
