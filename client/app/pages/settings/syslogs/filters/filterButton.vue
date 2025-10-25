<script setup lang="ts">
const { hexAToRGBA } = useColors();
const props = defineProps<{
  active?: boolean;
  color?: string;
  label?: string;
}>();
</script>

<template>
  <button
    class="rounded border text-sm font-semibold capitalize disabled:cursor-not-allowed"
    :class="{
      'border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400':
        !active,
      'border-primary-500 dark:border-primary-400 bg-primary-50 dark:bg-primary-900/25 text-primary-500':
        active,
    }"
    :style="{
      borderColor: active ? color : '',
      backgroundColor:
        active && color
          ? `rgba(${hexAToRGBA(color).r},${hexAToRGBA(color).g},${hexAToRGBA(color).b},0.15)`
          : '',
      color: active ? color : '',
    }"
  >
    <slot>
      <div class="flex items-center gap-1.5 py-1 pr-3 pl-2">
        <TIcon
          :name="active ? 'tabler:circle-filled' : 'tabler:circle'"
          class="h-4 w-4"
        />
        {{ label }}
      </div>
    </slot>
  </button>
</template>
