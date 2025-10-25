<script setup lang="ts">
const props = defineProps<{
  ln: number;
  lnWidth: number;
  noWrap: boolean;
  length: number;
}>();
</script>

<template>
  <div
    class="group flex items-center border-b border-neutral-500/10 font-mono text-sm"
  >
    <div
      class="sticky left-0 bg-neutral-50 pr-2 text-end font-bold text-neutral-500/40 select-none group-hover:text-neutral-500/75 dark:bg-neutral-800"
      :style="{
        minWidth: `${lnWidth + 8}px`,
      }"
    >
      {{ ln }}
    </div>
    <div
      :class="{ 'whitespace-nowrap': noWrap }"
      class="px-3"
      :style="{ minWidth: `${length * 7.7 + 24}px` }"
    >
      <slot></slot>
    </div>
  </div>
</template>
