<script setup lang="ts">
import type { Client } from "@/types/models";

const props = defineProps<{
  client: Client;
}>();
</script>

<template>
  <div class="space-y-1">
    <div class="flex items-center gap-2 px-2">
      <div class="font-semibold">Client Name:</div>
      <div class="flex-auto text-end font-mono">{{ client?.client_name }}</div>
    </div>
    <div class="flex items-center gap-2 px-2">
      <div class="font-semibold">Client Domain:</div>
      <div class="flex-auto text-end font-mono">
        {{ client?.client_domain }}
      </div>
    </div>
    <div class="ring-primary bg-primary-700/25 space-y-1 rounded ring">
      <div class="flex items-center gap-2 px-2">
        <div class="font-semibold">Client ID:</div>
        <div class="flex-auto text-end font-mono">
          {{ client?.client_id }}
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2 px-2">
        <div class="font-semibold">Client Secret:</div>
        <div class="flex-auto text-end font-mono text-sm">
          {{ client?.client_secret }}
        </div>
      </div>
    </div>
    <div
      class="border-warning-200 bg-warning-100 dark:border-warning-600 dark:bg-warning-700/25 rounded border px-3 py-1 text-center text-sm font-semibold"
    >
      Client secrets are only displayed once and cannot be retrieved. Make sure
      you save it somewhere secure.
    </div>
  </div>
</template>
