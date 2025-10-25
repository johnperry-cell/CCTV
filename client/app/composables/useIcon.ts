import type { IUseIcon } from "~/types";

type modifierItem = {
  search: RegExp;
  replace: (r: string | number) => string;
};

export const useIcon = (options: IUseIcon | null = null) => {
  const modifier: Record<string, modifierItem> = {
    strokeWidth: {
      search: /stroke-width="[^"]*"/g,
      replace: (r: string | number) => `stroke-width="${r}"`,
    },
    strokeColor: {
      search: /stroke="[^"]*"/g,
      replace: (r: string | number) => `stroke="${r}"`,
    },
    fill: {
      search: /fill="[^"]*"/g,
      replace: (r: string | number) => `fill="${r}"`,
    },
    animationDuration: {
      search: /animation-duration="[^"]*"/g,
      replace: (r: string | number) => `animation-duration="${r}"`,
    },
    opacity: {
      search: /opacity="[^"]*"/g,
      replace: (r: string | number) => `opacity="${r}"`,
    },
  };

  const validation: Record<string, boolean> = {
    strokeWidth: !!options?.strokeWidth && options.strokeWidth > 0,
    strokeColor: !!options?.strokeColor,
    fill: !!options?.fill,
    animationDuration:
      !!options?.animationDuration && options.animationDuration > 0,
    opacity: !!options?.opacity && options.opacity > 0,
  };

  const modify = (content: string, search: RegExp, replace: string) => {
    let result = `${content}`;
    if (!!search) {
      result = result.replace(search, replace);
    }
    return result;
  };

  const filter = (opts: IUseIcon): Record<string, boolean> => {
    return {
      strokeWidth: !!opts?.strokeWidth && opts.strokeWidth > 0,
      strokeColor: !!opts?.strokeColor,
      fill: !!opts?.fill,
      animationDuration:
        !!opts?.animationDuration && opts.animationDuration > 0,
      opacity: !!opts?.opacity && opts.opacity > 0,
    };
  };

  const customize = (content: string, opts: IUseIcon | null = null): string => {
    const _options = { ...options, ...opts };
    let result = `${content}`;
    if (!!_options) {
      let opts = Object.keys(_options).filter(
        (key: string) => filter(_options)[key]
      );
      opts.forEach((opt) => {
        result = modify(
          result,
          modifier[opt].search,
          modifier[opt].replace(_options[opt])
        );
      });
    }
    return result;
  };

  return { customize };
};
