<script setup lang="ts">
import type { LogFileItem } from "~/types/models";
import Logs from "./logs.vue";
const { $api } = useNuxtApp();
const { formatSize } = useUtils();

const props = defineProps<{
  file: LogFileItem;
}>();
const emit = defineEmits(["close", "download", "delete"]);

const loading = ref(false);

const logData = ref<Array<string>>([]);
const logSize = ref<number>(0);
const hasError = ref<boolean>(false);
const errorCode = ref<string>();

const loadLogData = () => {
  loading.value = true;
  hasError.value = false;
  errorCode.value = undefined;
  $api
    .get(`log/system/${props.file.name}`)
    .then((response) => {
      logData.value = response.data != "" ? response.data.split(/\r?\n/) : [];
      logSize.value = response.headers["content-length"] * 1;
    })
    .catch((error) => {
      hasError.value = !!error.response.data.size;
      logSize.value = error.response.data.size ?? 0;
      errorCode.value = error.response.data.code;
    })
    .finally(() => {
      loading.value = false;
    });
};

onMounted(() => {
  loadLogData();
});
</script>

<template>
  <div class="flex h-full w-full flex-col">
    <div class="flex items-center gap-2 bg-gray-100 px-3 py-1 dark:bg-gray-700">
      <TButton
        label="Back"
        icon="tabler:chevron-left"
        size="xs"
        variant="ghost"
        color="neutral"
        @click="emit('close')"
      />
      <TSeparator orientation="vertical" />
      <div class="flex-auto">
        {{ file.name }}
        <span class="px-2 text-xs text-gray-400">
          ({{ formatSize(file.size) }})
        </span>
      </div>
      <TButton
        icon="tabler:refresh"
        variant="ghost"
        color="neutral"
        size="xs"
        :loading
        @click="loadLogData"
      />
      <TButton
        icon="tabler:download"
        variant="ghost"
        color="neutral"
        size="xs"
        :disabled="loading"
        @click="emit('download', [file])"
      />
      <TButton
        icon="tabler:trash"
        variant="ghost"
        color="red"
        size="xs"
        :disabled="loading"
        @click="emit('delete', [file])"
      />
    </div>
    <div class="flex-auto">
      <div
        v-if="hasError"
        class="text-negative relative h-full text-center text-lg font-semibold"
      >
        Log file is too large to preview. Please download the file instead!
        <div
          class="text-primary/50 absolute top-0 right-[2.85rem] leading-none delay-100"
        >
          <TIcon name="tabler:chevrons-up" class="h-8 w-8 animate-bounce" />
        </div>
      </div>
      <div
        v-else-if="logData.length <= 0"
        class="text-center font-semibold text-neutral-400 italic"
      >
        No Logs!
      </div>
      <Logs v-else :log="logData" wrap />
    </div>
  </div>
</template>
