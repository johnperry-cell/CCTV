<script setup lang="ts">
const props = defineProps<{
  label: string;
  description: string;
  icon?: string;
}>();

const slots = useSlots();
</script>

<template>
  <TCard>
    <template #header>
      <div class="flex items-center gap-5">
        <TIcon v-if="icon" :name="icon" class="size-12" />
        <div class="flex-auto">
          <TTypography variant="h6" class="font-semibold">
            {{ label }}
          </TTypography>
          <p class="text-dimmed mt-1 text-sm">
            {{ description }}
          </p>
        </div>
      </div>
    </template>
    <slot></slot>
    <template v-if="!!slots.footer" #footer>
      <slot name="footer" />
    </template>
  </TCard>
</template>
