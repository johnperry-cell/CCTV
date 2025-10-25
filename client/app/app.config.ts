import type { PasswordRules } from "./types";
import { defu } from "defu";
import icons from "~/config/uiIcons";
import { useIcon } from "./composables/useIcon";
const { customize } = useIcon();

export default defineAppConfig({
  // ui: defu(
  //   {
  //     primary: "pine",
  //     gray: "steel",
  //     button: {
  //       base: "[&>*]:pointer-events-none",
  //       rounded: "rounded",
  //       variant: {
  //         solid: "dark:text-white ",
  //         outline: "dark:hover:bg-primary-300/20 ",
  //       },
  //     },
  //     card: {
  //       base: "border flex flex-col border-dark-200 dark:border-steel-700",
  //       divide: "divide-dark-100 dark:divide-gray-700",
  //       background: "bg-gray-50 dark:bg-gray-800",
  //       rounded: "rounded",
  //       ring: "dark:ring-gray-600",
  //       body: {
  //         base: "flex-auto",
  //         padding: "px-3 py-2 sm:p-4",
  //       },
  //       header: {
  //         base: "flex items-center justify-between py-2",
  //         padding: "px-3 py-2 sm:p-4",
  //       },
  //       footer: {
  //         padding: "px-3 py-2 sm:p-4",
  //       },
  //     },
  //     container: {
  //       base: "p-6 flex-1 grid gap-4",
  //       padding: "",
  //       constrained: "",
  //     },
  //     divider: {
  //       border: {
  //         base: "flex border-gray-200 dark:border-gray-700",
  //       },
  //     },
  //     input: {
  //       base: "relative block disabled:cursor-not-allowed disabled:opacity-75 focus:outline-none border-0 !shadow-none bg-gray-50 dark:bg-gray-700",
  //       rounded: "rounded",
  //       color: {
  //         white: {
  //           outline: "shadow-sm bg-gray-50 dark:bg-gray-700",
  //         },
  //       },
  //       size: {
  //         md: "text-base",
  //         lg: "text-base",
  //         xl: "text-lg",
  //       },
  //     },
  //     select: {
  //       wrapper: "max-w-[400px]",
  //       rounded: "rounded",
  //       color: {
  //         white: {
  //           outline: "bg-gray-50 dark:bg-gray-700",
  //         },
  //       },
  //     },
  //     selectMenu: {
  //       background: "bg-gray-50 dark:bg-gray-700",
  //     },
  //     textarea: {
  //       base: "relative block max-w-[400px] disabled:cursor-not-allowed disabled:opacity-75 focus:outline-none border-0 !shadow-none bg-gray-50 dark:bg-gray-700",
  //       rounded: "rounded",
  //       color: {
  //         white: {
  //           outline: "shadow-sm bg-gray-50 dark:bg-gray-700",
  //         },
  //       },
  //       size: {
  //         md: "text-base",
  //         lg: "text-base",
  //         xl: "text-lg",
  //       },
  //     },
  //     formGroup: {
  //       label: {
  //         base: "font-semibold",
  //       },
  //       error:
  //         "absolute top-[calc(100%_+_0.25rem)] animate-slide-down translate-y-0.5 mt-0 px-2 text-xs transition-all",
  //     },
  //     dropdown: {
  //       wrapper: "dark:bg-gray-700 rounded",
  //       rounded: "rounded",
  //       background: "bg-gray-50 dark:bg-gray-700",
  //       item: {
  //         size: "text-base",
  //       },
  //     },
  //     table: {
  //       base: "border border-gray-200 dark:border-gray-600",
  //       tr: {
  //         active: "hover:bg-gray-100 dark:hover:bg-gray-700/50",
  //       },
  //       th: {
  //         base: "bg-gray-100 dark:bg-gray-700",
  //       },
  //       tbody:
  //         "*:[&:not(:last-child)]:border-b *:!border-gray-200 *:dark:!border-gray-600",
  //     },
  //     pagination: {
  //       wrapper: "flex items-center gap-1",
  //       base: "!border-none",
  //       rounded:
  //         "!border-none !border-transparent min-w-[32px] justify-center !rounded-md",
  //       default: {
  //         activeButton: {
  //           variant: "solid",
  //           color: "primary",
  //         },
  //         inactiveButton: {
  //           variant: "solid",
  //           color: "gray",
  //         },
  //         prevButton: {
  //           color: "gray",
  //         },
  //         nextButton: {
  //           color: "gray",
  //         },
  //       },
  //     },
  //     popover: {
  //       background: "bg-gray-50 dark:bg-gray-700",
  //     },
  //   },
  //   uiIcons,
  // ),

  ui: defu(
    {
      colors: {
        primary: "pine",
        secondary: "azure",
        success: "leaf",
        info: "azure",
        warning: "sunflower",
        error: "sunset",
        neutral: "zinc",
      },
      formField: {
        slots: {
          error:
            "absolute top-full animate-slide-down translate-y-0.5 mt-0 px-2 text-xs transition-all",
          container: "[&>:has(input)]:w-full",
        },
      },
      card: {
        slots: {
          root: "flex flex-col relative overflow-visible",
          body: "flex-auto",
          header: "flex items-center gap-2",
          footer: "flex items-center gap-2",
        },
      },
      button: {
        slots: {
          base: ["select-none cursor-pointer"],
        },
      },
    },
    {
      icons,
    },
  ),

  theme: {
    radius: 0.125,
    blackAsPrimary: false,
  },

  icon: {
    mode: "svg",
    customize: (
      content: string,
      name: string,
      prefix: string,
      provider: string,
    ) => {
      if (prefix == "tabler") return customize(content, { strokeWidth: 1.5 });
      return content;
    },
  },

  mitd: {
    password: {
      rules: {
        /**
         * The minimum size of the password
         */
        min: 8,

        /**
         * The maximum size of the password
         * Defaults to null (no limit)
         */
        max: null,

        /**
         * Makes the password require at least one number
         */
        numbers: true,

        /**
         * Makes the password require at least one symbol
         */
        symbols: false,

        /**
         * Makes the password require at least one letter
         */
        letters: true,

        /**
         * Makes the password require at least one uppercase and one lowercase letter
         * ignored if 'letters' is false
         */
        mixedCase: false,

        /**
         * Default error Messages
         */
        messages: {
          min: "Password must be at least $1 characters",
          max: "Password must be at most $1 characters",
          numbers: "Password must contain at least one number",
          symbols: "Password must contain at least one symbol",
          letters: "Password must contain at least one letter",
          mixedCase:
            "Password must contain at least one uppercase and lowercase letter",
        },
      } as PasswordRules,
    },
  },
});
