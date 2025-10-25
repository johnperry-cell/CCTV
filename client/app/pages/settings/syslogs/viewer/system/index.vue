<script setup lang="ts">
import type { LogFileItem } from "~/types/models/logs";
import FileList from "./list.vue";
import Preview from "./preview.vue";
import DeleteLogs from "./delete.vue";
import type { AxiosProgressEvent } from "axios";

const emit = defineEmits(["close"]);
const { $api } = useNuxtApp();

const loading = ref(false);
const states = ref({
  download: {
    active: false,
    loaded: 0,
    total: 1,
  },
});
const preview = ref<LogFileItem>();
const files = ref<Array<LogFileItem>>([]);
const selected = ref<Array<LogFileItem>>([]);
const toDelete = ref<{
  show: boolean;
  files: Array<LogFileItem>;
}>({
  show: false,
  files: [],
});

const downloadState = ref({});

const viewLog = (e: LogFileItem) => {
  preview.value = e;
};

const downloadLogs = (e: Array<LogFileItem>) => {
  loading.value = true;
  states.value.download.active = true;
  const names = e.map((f) => f.name);
  let tmp: Array<LogFileItem> = [];
  if (selected.value.length > 0) {
    tmp = [...selected.value];
    selected.value = e;
  }
  $api
    .get(`log/download`, {
      params: { names },
      responseType: "blob",
      onDownloadProgress: (progressEvent: AxiosProgressEvent) => {
        if (progressEvent.lengthComputable) {
          states.value.download.total = progressEvent.total ?? 0;
          states.value.download.loaded = progressEvent.loaded ?? 0;
        }
      },
    })
    .then((response) => {
      // create file link in browser's memory
      const href = window.URL.createObjectURL(response.data);
      const fname = `system.${names.length > 1 ? "zip" : "log"}`;

      // create "a" HTML element with href to file & click
      const link = document.createElement("a");
      link.href = href;
      link.setAttribute("download", fname); //or any other extension
      document.body.appendChild(link);
      link.click();

      // clean up "a" element & remove ObjectURL
      document.body.removeChild(link);
      URL.revokeObjectURL(href);
    })
    .finally(() => {
      loading.value = false;
      states.value.download.total = 1;
      states.value.download.loaded = 0;
      states.value.download.active = false;
      if (selected.value.length > 0) {
        selected.value = [...tmp];
        tmp = [];
      }
    });
};

const deleteLogs = (e: Array<LogFileItem>) => {
  log(e);
  toDelete.value.files = e;
  toDelete.value.show = true;
};

const onDelete = (e: Array<LogFileItem>) => {
  files.value = e;
  preview.value = undefined;
  selected.value = [];
  toDelete.value.show = false;
};
</script>

<template>
  <TCard
    :ui="{
      root: 'h-screen-95',
      body: 'p-0',
      footer:
        'flex min-h-8 items-center gap-2 px-3 py-1 text-xs font-semibold text-neutral-500/75 italic',
    }"
  >
    <template #header>
      <TTypography variant="h3" class="flex-auto">System Logs</TTypography>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        size="sm"
        @click="emit('close')"
      />
    </template>
    <FileList
      v-if="!preview"
      v-model="files"
      v-model:selected="selected"
      v-model:loading="loading"
      v-bind="states"
      @download="downloadLogs"
      @delete="deleteLogs"
      @view="viewLog"
    />
    <Preview
      v-else
      :file="preview!"
      @download="downloadLogs"
      @delete="deleteLogs"
      @close="preview = undefined"
    />

    <TModal
      v-model:open="toDelete.show"
      title="Delete Log"
      description="Deletes logs"
    >
      <template #content="{ close }">
        <DeleteLogs
          :files="toDelete.files"
          @close="close()"
          @deleted="onDelete"
        />
      </template>
    </TModal>
  </TCard>
</template>
