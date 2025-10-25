<script setup lang="ts">
import { useDebounceFn } from "@vueuse/core";

import FilterButton from "./filterButton.vue";

const props = defineProps<{
  moduleOptions: Array<string>;
  cols: number;
}>();
const modules = defineModel<Array<string>>({
  required: true,
});

const debouncedMods = ref<Array<string>>([...modules.value]);

const moduleDebounced = useDebounceFn(() => {
  modules.value = [...new Set(debouncedMods.value)];
}, 750);

const selectModule = (l: string) => {
  if (moduleSelected(l)) {
    debouncedMods.value = debouncedMods.value.filter((i) => i != l);
  } else {
    debouncedMods.value.push(l);
  }
};

const moduleSelected = (l: string): boolean => debouncedMods.value.includes(l);

watch(debouncedMods, moduleDebounced, { deep: true });
</script>

<template>
  <div
    class="grid gap-x-0.5 gap-y-1 py-1"
    :style="{
      gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
    }"
  >
    <template v-for="(module, index) in moduleOptions" :key="module">
      <slot
        :active="moduleSelected(module)"
        :label="module"
        :disabled="moduleSelected(module) && debouncedMods.length == 1"
        :action="() => selectModule(module)"
      >
        <FilterButton
          :active="moduleSelected(module)"
          :label="module"
          :disabled="moduleSelected(module) && debouncedMods.length == 1"
          @click="selectModule(module)"
        />
      </slot>
    </template>
  </div>
</template>
