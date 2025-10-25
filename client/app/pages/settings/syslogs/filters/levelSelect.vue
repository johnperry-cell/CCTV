<script setup lang="ts">
import { useDebounceFn } from "@vueuse/core";

import FilterButton from "./filterButton.vue";
const props = defineProps<{
  levelMap: Record<number, string>;
  colors?: Record<number, string>;
  cols: number;
}>();
const levels = defineModel<number[]>({
  required: true,
});

const debouncedLevels = ref<number[]>([...levels.value]);

const levelDebounced = useDebounceFn(() => {
  levels.value = [...new Set(debouncedLevels.value)].map((l) => l * 1);
}, 750);

const selectLevel = (l: number) => {
  if (levelSelected(l)) {
    debouncedLevels.value = debouncedLevels.value.filter((i) => i != l * 1);
  } else {
    debouncedLevels.value.push(l * 1);
  }
};

const levelSelected = (l: number): boolean =>
  debouncedLevels.value.includes(l * 1);

watch(debouncedLevels, levelDebounced, { deep: true });
</script>

<template>
  <div
    class="grid gap-x-0.5 gap-y-1 py-1"
    :style="{
      gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
    }"
  >
    <template v-for="(level, index) in levelMap" :key="level">
      <slot
        :active="levelSelected(index)"
        :label="level"
        :disabled="levelSelected(index) && debouncedLevels.length == 1"
        :action="() => selectLevel(index)"
      >
        <FilterButton
          :active="levelSelected(index)"
          :color="colors?.[index] ?? '#999'"
          :label="level"
          :disabled="levelSelected(index) && debouncedLevels.length == 1"
          @click="selectLevel(index)"
        />
      </slot>
    </template>
  </div>
</template>
