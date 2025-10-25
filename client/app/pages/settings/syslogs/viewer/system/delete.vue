<script setup lang="ts">
import type { LogFileItem } from "~/types/models";

const props = defineProps<{
  files: Array<LogFileItem>;
}>();
const emit = defineEmits(["close", "deleted"]);

const { $api } = useNuxtApp();
const { formatSize } = useUtils();

const loading = ref(false);

const deleteLogs = () => {
  loading.value = true;

  $api
    .delete("/log/system", {
      data: {
        names: props.files.map((f) => f.name),
      },
    })
    .then((response) => {
      emit("deleted", response.data);
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard
    :ui="{
      root: 'divide-y divide-neutral-400/25 overflow-hidden',
      header:
        'relative flex items-center bg-neutral-100 px-4 py-3 sm:py-2 dark:bg-neutral-900',
      footer: 'flex items-center justify-end gap-2 px-4 py-3 sm:py-2',
    }"
  >
    <template #header>
      <div
        class="text-error-500 dark:text-error-400 flex flex-auto items-center gap-2"
      >
        <TIcon name="tabler:alert-triangle" class="h-6 w-6" />
        <h3 class="text-base leading-6 font-semibold">
          Are you sure you want to delete these logs?
        </h3>
      </div>
      <TButton
        color="neutral"
        variant="ghost"
        icon="i-heroicons-x-mark-20-solid"
        size="xs"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <div class="flex flex-col gap-2 px-12 py-2">
      You are about to delete the following log files:
      <div
        class="flex flex-col divide-y divide-neutral-200 rounded-lg border border-neutral-200 bg-neutral-100 px-3 py-1 dark:border-neutral-600 dark:bg-neutral-700"
      >
        <div v-for="file in files" :key="file.name" class="py-1">
          {{ file.name }}
          <span class="px-3 text-xs text-neutral-400 italic">
            ({{ formatSize(file.size) }})
          </span>
        </div>
      </div>
      This action is irreversible. Continue?
    </div>

    <TInnerLoading :active="loading" text="Deleting log file(s)..." />

    <template #footer>
      <TButton
        label="Delete"
        color="error"
        :disabled="loading"
        :loading
        @click="deleteLogs"
      />
      <TButton
        label="Cancel"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
