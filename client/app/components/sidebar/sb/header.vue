<script setup lang="ts">
defineProps<{
  expand: boolean;
  title: string;
}>();

defineEmits(["close"]);
</script>

<template>
  <div class="flex items-center gap-[0.625rem] p-3">
    <img src="/favicons/baguioseal.svg" class="h-12 min-h-12 w-12 min-w-12" />
    <span
      class="line-clamp-1 flex-auto whitespace-nowrap break-all text-left transition-all"
      :class="{ 'pointer-events-none opacity-0': !expand }"
    >
      {{ title }}
    </span>
    <TButton
      v-if="expand"
      size="sm"
      square
      color="neutral"
      variant="link"
      icon="tabler:x"
      @click="$emit('close')"
    />
  </div>
</template>
