<script setup lang="ts">
import {
  ColorPicker,
  type ColorChangeDetail,
  type ColorMap,
  type ColorRgb,
} from "vue-accessible-color-picker";
import type { HasKey } from "~/types";

const model = defineModel<string>();
const emit = defineEmits(["change"]);
const props = defineProps<{
  expandHeight?: boolean;
}>();

const toast = useToast();
const { randomColor, RGBAToHexA, isDark } = useColors();

const open = ref<boolean>(false);
const currentColor = ref<string>(model.value ?? randomColor());
const colorMap = ref<ColorMap>();
const colorPicker = ref<any>(null);
const rgba = ref<ColorRgb & HasKey>({
  r: 0,
  g: 0,
  b: 0,
  a: 1,
});

const colorIsDark = computed(() => isDark(currentColor.value));
const modelIsDark = computed(() => isDark(model.value!));

const onColorChange = (color: ColorChangeDetail) => {
  currentColor.value = color.cssColor;
  colorMap.value = color.colors;
  rgba.value = {
    r: Math.round(color.colors?.rgb.r ?? 0),
    g: Math.round(color.colors?.rgb.g ?? 0),
    b: Math.round(color.colors?.rgb.b ?? 0),
    a: color.colors?.rgb.a ?? 1,
  };
  colorPicker.value?.$el.style.setProperty(
    "--vacp-color-alpha",
    `hsl(${color.colors.hsl.h} ${color.colors.hsl.s.toFixed(2)}% ${color.colors.hsl.l.toFixed(2)}%)`,
  );
  emit("change", color.colors);
};

const updateRgb = (space: "r" | "g" | "b" | "a", val: number) => {
  if (isNaN(val)) return;
  let v = Math.min(255, Math.max(0, val));
  if (space == "a") {
    v = Math.min(100, Math.max(0, val)) / 100;
  }
  rgba.value[space] = v;
  const { r, g, b, a } = rgba.value;
  currentColor.value = RGBAToHexA(r, g, b, a);
};

const copyColor = () => {
  navigator.clipboard.writeText(currentColor.value);

  toast.add({
    title: "Color copied!",
    description: currentColor.value,
    color: "primary",
    icon: "tabler:copy",
  });
};

watch(open, (val) => {
  if (val && model.value) {
    currentColor.value = model.value;
  }
});

onMounted(() => {
  if (!model.value) model.value = currentColor.value;
});
</script>

<template>
  <TPopover v-model:open="open">
    <slot
      :color="currentColor"
      :colorMap="colorMap"
      :dark="colorIsDark"
      :isDark="(hex: string) => isDark(hex)"
    >
      <TButton
        icon="tabler:color-picker"
        class="w-full flex-wrap content-end justify-end"
        :class="{
          'h-[80px]': expandHeight,
          'text-neutral-200': modelIsDark,
          'text-neutral-700': !modelIsDark,
        }"
        :style="{ backgroundColor: model }"
      />
    </slot>

    <template #content>
      <div class="select-none p-1">
        <div class="flex items-center justify-between">
          <TTypography variant="sm" class="px-2 leading-tight">
            Color picker
          </TTypography>
          <TButton
            icon="tabler:x"
            variant="ghost"
            :padded="false"
            color="neutral"
            size="xs"
            class="p-1"
            @click="open = false"
          />
        </div>

        <div class="relative flex justify-center">
          <ColorPicker
            ref="colorPicker"
            :color="currentColor"
            :visible-formats="['hex']"
            default-format="hex"
            @colorChange="onColorChange"
          >
          </ColorPicker>
          <div
            class="vacp-color-preview absolute bottom-1 right-1 h-10 w-14 -translate-x-[0.5px] border border-black bg-neutral-300 dark:border-neutral-100"
            :style="{ backgroundColor: currentColor }"
            @click="currentColor = randomColor()"
          >
            <div
              class="h-full w-full"
              :style="{ backgroundColor: currentColor }"
            ></div>
          </div>
        </div>

        <div class="flex items-center gap-1 p-2">
          <div class="flex-auto text-center">
            <label
              for="vacp-inpute-rgb-r"
              class="flex cursor-text select-none flex-col items-center rounded-lg border border-neutral-400/25 bg-neutral-50 px-2 py-0.5 text-sm dark:border-neutral-200/25 dark:bg-neutral-700"
            >
              <input
                id="vacp-inpute-rgb-r"
                type="number"
                class="w-full appearance-none bg-transparent bg-none outline-none"
                :value="rgba.r"
                :min="0"
                :max="255"
                @input="
                  updateRgb('r', +($event.target as HTMLInputElement).value)
                "
              />
            </label>
            <TTypography variant="xs" class="leading-tight">R</TTypography>
          </div>

          <div class="flex-auto text-center">
            <label
              for="vacp-inpute-rgb-g"
              class="flex cursor-text select-none flex-col items-center rounded-lg border border-neutral-400/25 bg-neutral-50 px-2 py-0.5 text-sm dark:border-neutral-200/25 dark:bg-neutral-700"
            >
              <input
                id="vacp-inpute-rgb-g"
                type="number"
                class="w-full appearance-none bg-transparent bg-none outline-none"
                :value="rgba.g"
                :min="0"
                :max="255"
                @input="
                  updateRgb('g', +($event.target as HTMLInputElement).value)
                "
              />
            </label>
            <TTypography variant="xs" class="leading-tight">G</TTypography>
          </div>

          <div class="flex-auto text-center">
            <label
              for="vacp-inpute-rgb-b"
              class="flex cursor-text select-none flex-col items-center rounded-lg border border-neutral-400/25 bg-neutral-50 px-2 py-0.5 text-sm dark:border-neutral-200/25 dark:bg-neutral-700"
            >
              <input
                id="vacp-inpute-rgb-b"
                type="number"
                class="w-full appearance-none bg-transparent bg-none outline-none"
                :value="rgba.b"
                :min="0"
                :max="255"
                @input="
                  updateRgb('b', +($event.target as HTMLInputElement).value)
                "
              />
            </label>
            <TTypography variant="xs" class="leading-tight">B</TTypography>
          </div>

          <div class="flex-auto text-center">
            <label
              for="vacp-inpute-rgb-a"
              class="flex cursor-text select-none flex-col items-center rounded-lg border border-neutral-400/25 bg-neutral-50 px-2 py-0.5 text-sm dark:border-neutral-200/25 dark:bg-neutral-700"
            >
              <input
                id="vacp-inpute-rgb-a"
                type="number"
                class="w-full appearance-none bg-transparent bg-none outline-none"
                :value="parseInt(`${rgba.a * 100}`)"
                :min="0"
                :max="100"
                pattern="[0-9]*"
                @input="
                  updateRgb('a', +($event.target as HTMLInputElement).value)
                "
              />
            </label>
            <TTypography variant="xs" class="leading-tight">A</TTypography>
          </div>
        </div>
        <div class="flex items-start justify-between gap-1 p-2">
          <div class="text-center">
            <label
              for="vacp-inpute-hex"
              class="relative flex w-28 cursor-text select-none flex-col items-center rounded-lg border border-neutral-400/25 bg-neutral-50 px-2 py-0.5 text-sm dark:border-neutral-200/25 dark:bg-neutral-700"
            >
              <input
                id="vacp-inpute-hex"
                type="text"
                class="w-full bg-transparent bg-none outline-none"
                v-model="currentColor"
                maxlength="9"
              />
            </label>
            <TTypography variant="xs" class="leading-tight">HEX</TTypography>
          </div>

          <div class="flex items-center gap-2">
            <TButton
              color="neutral"
              icon="tabler:copy"
              :ui="{ base: 'flex items-center justify-center' }"
              @click="copyColor"
            >
            </TButton>
            <TButton
              color="primary"
              :ui="{ base: 'flex w-16 items-center justify-center' }"
              @click="[(model = currentColor), (open = false)]"
            >
              OK
            </TButton>
          </div>
        </div>
      </div>
    </template>
  </TPopover>
</template>

<style>
@reference "~/assets/css/tailwind.css"

:root {
  --vacp-color-background: var(--color-gray-50);
  --vacp-font-family: inherit;
  --vacp-spacing: 0.25rem;
  --vacp-color-focus: #0000;
}

:root[class="dark"] {
  --vacp-color-background: var(--color-gray-800);
}

.vacp-color-picker {
  @apply relative flex w-full max-w-full flex-col gap-2;
}

.vacp-color-picker .vacp-color-space {
  @apply aspect-video h-32 rounded-sm !shadow-none ring-1;
}

.vacp-color-picker .vacp-color-space .vacp-color-space-thumb {
  @apply border-[1px] ring-1;
}

.vacp-color-picker .vacp-color-space .vacp-color-space-thumb:focus {
  @apply shadow-none;
}

.vacp-color-picker .vacp-range-input-group {
  @apply pointer-events-none flex flex-col gap-2 pr-16;
}

.vacp-color-picker .vacp-range-input-group .vacp-range-input-label {
  @apply m-0;
}

.vacp-color-picker
  .vacp-range-input-group
  .vacp-range-input-label
  .vacp-range-input-label-text {
  @apply hidden;
}

.vacp-color-picker
  .vacp-range-input-group
  .vacp-range-input-label
  .vacp-range-input {
  @apply pointer-events-auto m-0;
  --vacp-slider-track-height: 1rem;
  --vacp-slider-thumb-size: calc(1.25rem - var(--vacp-width-border, 1px) * 2);
}

.vacp-color-picker
  .vacp-range-input-group
  .vacp-range-input-label
  .vacp-range-input:focus::-moz-range-track,
.vacp-color-picker
  .vacp-range-input-group
  .vacp-range-input-label
  .vacp-range-input:focus::-webkit-slider-runnable-track,
.vacp-color-picker
  .vacp-range-input-group
  .vacp-range-input-label
  .vacp-range-input:focus::-ms-track {
  outline: none;
}

.vacp-color-picker .vacp-range-input-group .vacp-range-input-label--alpha {
  --vacp-color: var(--vacp-color-alpha, transparent);
}

.vacp-color-picker .vacp-copy-button {
  display: none !important;
}

.vacp-color-picker .vacp-color-inputs {
  display: none !important;
}

.vacp-color-preview {
  background-image:
    linear-gradient(
      45deg,
      #eee 25%,
      transparent 25%,
      transparent 75%,
      #eee 75%,
      #eee
    ),
    linear-gradient(
      45deg,
      #eee 25%,
      transparent 25%,
      transparent 75%,
      #eee 75%,
      #eee
    );
  background-size: calc(0.125rem * 2) calc(0.125rem * 2);
  background-position:
    0 0,
    0.125rem 0.125rem;
}
</style>
