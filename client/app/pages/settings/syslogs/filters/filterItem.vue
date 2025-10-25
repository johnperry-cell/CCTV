<script setup lang="ts">
const props = defineProps<{
  icon: string;
  label: string;
  initialOpen?: boolean;
}>();

const view = ref(props.initialOpen ?? false);
</script>

<template>
  <div>
    <TButton
      variant="ghost"
      color="neutral"
      :padded="false"
      :ui="{ base: 'w-full py-3' }"
      @click="view = !view"
    >
      <div class="flex flex-auto items-center gap-1.5">
        <TIcon :name="icon" class="h-5 w-5" />
        <span class="font-semibold">{{ label }}</span>
      </div>
      <TIcon
        name="tabler:chevron-right"
        class="h-5 w-5 transition-transform"
        :class="{ 'rotate-90': view }"
      />
    </TButton>
    <TCollapse v-model="view">
      <slot />
    </TCollapse>
  </div>
</template>
