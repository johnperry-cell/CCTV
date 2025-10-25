<script setup lang="ts">
import colors from "tailwindcss/colors";
import { omit } from "#ui/utils";

import ThemePickerButton from "./themePickerButton.vue";
import ThemeModes from "./themeModes.vue";

const appConfig = useAppConfig();

const neutralColors = [
  "slate",
  "gray",
  "zinc",
  "neutral",
  "stone",
  "steel",
  "sepia",
];
const neutral = computed({
  get() {
    return appConfig.ui.colors.neutral;
  },
  set(option) {
    appConfig.ui.colors.neutral = option;
  },
});

const colorsToOmit = [
  "inherit",
  "current",
  "transparent",
  "black",
  "white",
  ...neutralColors,
];
const primaryColors = Object.keys(omit(colors, colorsToOmit as any));
const primary = computed({
  get() {
    return appConfig.ui.colors.primary;
  },
  set(option) {
    appConfig.ui.colors.primary = option;
    setBlackAsPrimary(false);
  },
});

const radiuses = [0, 0.125, 0.25, 0.375, 0.5];
const radius = computed({
  get() {
    return appConfig.theme.radius;
  },
  set(option) {
    appConfig.theme.radius = option;
  },
});

function setBlackAsPrimary(value: boolean) {
  appConfig.theme.blackAsPrimary = value;
}
</script>

<template>
  <TPopover :ui="{ content: 'flex w-72 flex-col gap-4 p-4' }">
    <template #default="{ open }">
      <TButton
        icon="i-tabler-color-swatch"
        color="neutral"
        :variant="open ? 'soft' : 'ghost'"
        square
        aria-label="Color picker"
        :ui="{ leadingIcon: 'text-primary size-6' }"
      />
    </template>

    <template #content>
      <fieldset>
        <legend class="mb-2 px-2 text-[11px] font-semibold leading-none">
          Primary
        </legend>

        <div class="grid grid-cols-3 gap-1">
          <ThemePickerButton
            label="Black"
            :selected="appConfig.theme.blackAsPrimary"
            @click="setBlackAsPrimary(true)"
          >
            <template #leading>
              <span
                class="inline-block h-2 w-2 rounded-full bg-black dark:bg-white"
              />
            </template>
          </ThemePickerButton>

          <ThemePickerButton
            v-for="color in primaryColors"
            :key="color"
            :label="color"
            :chip="color"
            :selected="!appConfig.theme.blackAsPrimary && primary === color"
            @click="primary = color"
          />
        </div>
      </fieldset>

      <fieldset>
        <legend class="mb-2 px-2 text-[11px] font-semibold leading-none">
          Neutral
        </legend>

        <div class="grid grid-cols-3 gap-1">
          <ThemePickerButton
            v-for="color in neutralColors"
            :key="color"
            :label="color"
            :chip="color === 'neutral' ? 'old-neutral' : color"
            :selected="neutral === color"
            @click="neutral = color"
          />
        </div>
      </fieldset>

      <fieldset>
        <legend class="mb-2 px-2 text-[11px] font-semibold leading-none">
          Radius
        </legend>

        <div class="grid grid-cols-5 gap-1">
          <ThemePickerButton
            v-for="r in radiuses"
            :key="r"
            :label="String(r)"
            class="justify-center px-0"
            :selected="radius === r"
            @click="radius = r"
          />
        </div>
      </fieldset>

      <ThemeModes title="Theme" />
    </template>
  </TPopover>
</template>
