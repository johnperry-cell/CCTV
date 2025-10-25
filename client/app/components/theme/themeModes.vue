<script setup lang="ts">
const props = defineProps<{
  title?: string;
}>();

const appConfig = useAppConfig();
const colorMode = useColorMode();

const modes = [
  { label: "light", icon: appConfig.ui.icons.light },
  { label: "dark", icon: appConfig.ui.icons.dark },
  { label: "system", icon: appConfig.ui.icons.system },
];
const mode = computed({
  get() {
    return colorMode.value;
  },
  set(option) {
    colorMode.preference = option;
  },
});

const tag = computed(() => (props.title ? "fieldset" : "div"));
</script>

<template>
  <component :is="tag">
    <legend
      v-if="title"
      class="mb-2 px-2 text-[11px] font-semibold leading-none"
    >
      {{ title }}
    </legend>

    <div class="grid grid-cols-3 gap-1">
      <ThemePickerButton
        v-for="m in modes"
        :key="m.label"
        v-bind="m"
        :selected="colorMode.preference === m.label"
        @click="mode = m.label"
      />
    </div>
  </component>
</template>
