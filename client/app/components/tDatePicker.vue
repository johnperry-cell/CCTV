<script setup lang="ts">
import type { DarkModeClassConfig } from "vue-screen-utils";
import { DatePicker as VCalendarDatePicker } from "v-calendar";

const props = withDefaults(
  defineProps<{
    transparent?: boolean;
    borderless?: boolean;
    isDark?: DarkModeClassConfig | boolean | "system";
    color?: string;
    timezone?: string;
  }>(),
  {
    transparent: true,
    borderless: true,
    isDark: () => ({ selector: "html", darkClass: "dark" }),
    color: "primary",
    timezone: "Asia/Manila",
  },
);

const emit = defineEmits(["close"]);
</script>

<template>
  <VCalendarDatePicker
    :transparent
    :borderless
    :isDark
    :color
    :timezone
    @update:modelValue="emit('close')"
    @dayclick="
      (day, event) => {
        event.target.blur();
      }
    "
  />
</template>
