import { useStorage } from "@vueuse/core";
import colors from "tailwindcss/colors";

export default defineNuxtPlugin(() => {
  const { theme, ui } = useAppConfig();
  const themeStore = useStorage("theme", {
    primary: ui.colors.primary,
    neutral: ui.colors.neutral,
    radius: theme.radius,
    blackAsPrimary: theme.blackAsPrimary,
  });
  const colorMode = useColorMode();
  const themeCookie = useStorage("color.mode", colorMode.preference);

  const color = computed(() =>
    colorMode.value === "dark"
      ? (colors as any)[ui.colors.neutral][900]
      : "white",
  );
  const radius = computed(
    () => `:root { --ui-radius: ${themeStore.value.radius}rem; }`,
  );
  const blackAsPrimary = computed(() =>
    themeStore.value.blackAsPrimary
      ? `:root { --ui-primary: black; } .dark { --ui-primary: white; }`
      : ":root {}",
  );

  theme.blackAsPrimary = themeStore.value.blackAsPrimary;
  theme.radius = themeStore.value.radius;
  ui.colors.primary = themeStore.value.primary;
  ui.colors.neutral = themeStore.value.neutral;

  watch(
    [() => theme, () => ui],
    () => {
      themeStore.value = {
        primary: ui.colors.primary,
        neutral: ui.colors.neutral,
        radius: theme.radius,
        blackAsPrimary: theme.blackAsPrimary,
      };
    },
    { deep: true },
  );

  watch(themeCookie, (val) => {
    const prefersDarkMode =
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches;

    colorMode.preference = val || (prefersDarkMode ? "dark" : "light");
  });

  useHead({
    meta: [
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { key: "theme-color", name: "theme-color", content: color },
    ],
    style: [
      { innerHTML: radius, id: "ui-radius", tagPriority: -2 },
      {
        innerHTML: blackAsPrimary,
        id: "ui-black-as-primary",
        tagPriority: -2,
      },
    ],
  });

  window.addEventListener("storage", (e) => {
    if (e.key === "theme") {
      const newValue = JSON.parse(e.newValue!);
      ui.colors.primary = newValue?.primary ?? "pine";
      ui.colors.neutral = newValue?.neutral ?? "steel";
      theme.radius = newValue?.radius ?? 0.25;
      theme.blackAsPrimary = newValue?.blackAsPrimary ?? false;

      themeStore.value = {
        primary: ui.colors.primary,
        neutral: ui.colors.neutral,
        radius: theme.radius,
        blackAsPrimary: theme.blackAsPrimary,
      };
    }
  });
});
