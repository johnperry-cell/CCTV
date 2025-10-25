<script setup lang="ts">
import { useImage, type UseImageOptions } from "@vueuse/core";
import { type ClassNameValue } from "tailwind-merge";

export type uiType = {};

const props = defineProps<
  Partial<UseImageOptions> & {
    icon?: string;
    disabled?: boolean;
    ui?: Record<string, ClassNameValue>;
  }
>();

const ui = useAppConfig().ui;

const imgOptions = computed<UseImageOptions>(
  () =>
    ({
      ...props,
      src: props.src ?? "",
    }) as UseImageOptions,
);

const { isLoading, error } = useImage(imgOptions);

const _icon = computed(() => {
  if (isLoading.value) {
    return ui.icons.loading;
  } else if (props.icon && !props.src) {
    return props.icon;
  } else if (error.value) {
    return "tabler:exclamation-mark";
  } else {
    return undefined;
  }
});
</script>

<template>
  <span
    class="group relative inline-flex aspect-square h-24 w-24 items-center justify-center overflow-hidden rounded-full ring-4 ring-neutral-200 dark:ring-neutral-600"
    :class="{
      '': disabled,
      'cursor-pointer': !disabled,
    }"
  >
    <img
      v-if="!isLoading && !!imgOptions.src"
      :alt="alt ?? 'user_avatar'"
      :src="imgOptions.src"
      class="h-full w-full object-cover"
    />

    <div
      v-else-if="_icon"
      class="pointer-events-none absolute inset-0 flex items-center justify-center text-gray-400"
    >
      <TIcon
        :name="_icon"
        class=""
        :class="{
          'h-8 w-8 animate-spin': isLoading,
          'h-16 w-16': !isLoading,
        }"
      />
    </div>
    <div
      v-if="!disabled"
      class="pointer-events-none absolute inset-0 flex items-end justify-stretch"
    >
      <div
        class="absolute inset-x-0 bottom-0 h-7 bg-gray-950 opacity-50 transition-opacity group-hover:opacity-50 md:opacity-0"
      />
      <TIcon
        name="tabler:camera-filled"
        class="absolute bottom-1 left-1/2 h-5 w-5 -translate-x-1/2 text-white opacity-100 transition-opacity group-hover:opacity-100 md:opacity-0"
      />
    </div>
  </span>
</template>
