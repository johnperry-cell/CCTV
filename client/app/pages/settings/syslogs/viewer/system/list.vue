<script setup lang="ts">
import type { LogFileItem } from "~/types/models";

const props = defineProps<{
  download: {
    active: boolean;
    loaded: number;
    total: number;
  };
  selected: Array<LogFileItem>;
}>();
const emit = defineEmits(["view", "download", "delete", "update:selected"]);

const { formatSize } = useUtils();
const { $api } = useNuxtApp();

const files = defineModel<Array<LogFileItem>>({
  required: true,
});
const selected = defineModel<Array<LogFileItem>>("selected", {
  required: true,
});
const loading = defineModel<boolean>("loading");
const downloading = ref<LogFileItem>();

const loadFileList = () => {
  loading.value = true;
  $api
    .get("log/files")
    .then((response) => {
      files.value = response.data;
    })
    .finally(() => {
      loading.value = false;
    });
};

const columns = ref([
  {
    accessorKey: "name",
    header: "Name",
    class: "",
    rowClass: "",
  },
  {
    accessorKey: "modified",
    header: "Modified",
    class: "w-48",
    rowClass: "",
  },
  {
    accessorKey: "size",
    header: "Size",
    class: "w-28",
    rowClass: "text-right",
  },
  {
    accessorKey: "actions",
    header: "",
    class: "w-28",
    rowClass: "",
  },
]);

const selectFile = (file: LogFileItem) => {
  if (isSelected(file)) {
    selected.value = selected.value?.filter((f) => f.name !== file.name) ?? [];
  } else {
    selected.value = [...(selected.value ?? []), file];
  }
};

const isSelected = (file: LogFileItem) => {
  return !!selected.value?.find((f) => f.name === file.name);
};

const dl = (file: LogFileItem) => {
  emit("download", [file]);
};

onMounted(() => {
  loadFileList();
});
</script>

<template>
  <div class="">
    <div class="flex items-center gap-2 px-3 py-1">
      <div class="flex flex-auto items-center gap-2 py-2">
        <TButton
          icon="tabler:download"
          label="Download Selected"
          variant="outline"
          color="primary"
          size="xs"
          :disabled="selected?.length <= 0"
          @click="emit('download', selected)"
        />
        <TButton
          icon="tabler:download"
          label="Delete Selected"
          variant="outline"
          color="error"
          size="xs"
          :disabled="selected?.length <= 0"
          @click="emit('delete', selected)"
        />
      </div>
      <TButton
        icon="tabler:refresh"
        variant="ghost"
        color="neutral"
        size="sm"
        @click="loadFileList"
      />
    </div>

    <TTable
      :columns
      :data="files"
      :loading
      :ui="{
        thead:
          '[&>tr]:divide-x [&>tr]:divide-neutral-300 [&>tr]:dark:divide-neutral-600',
        tr: 'hover:bg-neutral-100 dark:hover:bg-neutral-700/50',
        th: 'py-2',
        td: 'py-2',
      }"
    >
      <template #name-cell="{ row }">
        <div class="flex items-center gap-1.5">
          <TCheckbox
            :modelValue="isSelected(row.original)"
            @update:modelValue="selectFile(row.original)"
          />
          <TIcon name="tabler:file-text-filled" class="h-5 w-5" />
          <div>{{ row.original.name }}</div>
        </div>
      </template>
      <template #size-cell="{ row }">
        {{ formatSize(row.original.size) }}
      </template>

      <template #actions-cell="{ row }">
        <div class="flex items-center gap-1">
          <TButton
            icon="tabler:eye"
            variant="ghost"
            color="neutral"
            size="xs"
            :disabled="download.active"
            @click="emit('view', row.original)"
          />
          <TButton
            icon="tabler:download"
            variant="ghost"
            color="neutral"
            size="xs"
            :loading="download.active && isSelected(row.original)"
            :disabled="download.active"
            @click="dl(row.original)"
          />
          <TButton
            icon="tabler:trash"
            variant="ghost"
            color="error"
            size="xs"
            :disabled="download.active"
            @click="emit('delete', [row.original])"
          />
        </div>
      </template>
    </TTable>
  </div>
</template>
