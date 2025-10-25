<script setup lang="ts">
const pages = computed(() => ({
  UploaderPage: defineAsyncComponent(() => import("./uploader.vue")),
}));

const page = ref();
</script>

<template>
  <div class="h-full px-3 py-2">
    <TButton label="Uploader" @click="page = 'UploaderPage'" />
    <component v-if="page" :is="pages[page]" />
  </div>
</template>
>
