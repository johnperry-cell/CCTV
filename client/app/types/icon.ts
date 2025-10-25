import type { HasKey, Prettify } from "./utils";

export type IIconProps = {
  name: string;
  mode?: "svg" | "css" | null;
  size?: number | string | null;
  customize?: Function | null;
};

export type IUseIcon = Prettify<
  HasKey & {
    strokeWidth?: number;
    strokeColor?: string;
    fill?: string;
    animationDuration?: number;
    opacity?: number;
  }
>;
