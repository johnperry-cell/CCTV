import type { App, DirectiveBinding } from "vue";
import { twMerge } from "tailwind-merge";
import type { DirectiveFn, RippleOptions } from "./types";

const directive: DirectiveFn = (vue: App) => {
  const { isValidTag } = useUtils();
  const config = {
    base: "absolute -translate-x-1/2 bg-opacity-25 -translate-y-1/2 rounded-full animate-ripple pointer-events-none",
    class: "dark:bg-gray-100 bg-gray-800",
  };

  const ripple = (el: HTMLElement, options?: RippleOptions) => {
    const defaultOpts = {
      tag: "span",
      class: config.class,
      disabled: false,
    };
    const opts: RippleOptions = Object.assign(defaultOpts, options);

    const handle = (e: MouseEvent) => {
      if (opts.disabled !== true) {
        const x = e.offsetX;
        const y = e.offsetY;
        const tg: string = isValidTag(opts.tag!) ? opts.tag! : defaultOpts.tag;
        const rippler = document.createElement(tg);
        rippler.setAttribute("style", `top:${y}px; left:${x}px`);

        const classes = twMerge(config.base, opts.class);
        if (classes !== "") {
          rippler.className = classes;
        }

        el.appendChild(rippler);
        setTimeout(() => rippler.remove(), 250);
      }
    };

    return handle;
  };

  vue.directive("ripple", {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
      el.addEventListener("click", ripple(el, binding.value));
    },
    beforeUnmount(el: HTMLElement, binding: DirectiveBinding) {
      el.removeEventListener("click", ripple(el, binding.value));
    },
  });
};

export default directive;
