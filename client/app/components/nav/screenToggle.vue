<script setup lang="ts">
import { useFullscreen } from "@vueuse/core";
const { isFullscreen, enter, exit, toggle } = useFullscreen();
</script>
<template>
  <TButton
    color="neutral"
    variant="ghost"
    size="md"
    square
    :icon="isFullscreen ? 'tabler:minimize' : 'tabler:maximize'"
    :ui="{ leadingIcon: 'size-6' }"
    @click="toggle"
  />
</template>
