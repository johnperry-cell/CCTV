<script setup lang="ts">
import type { LogData } from "~/types/models";
import Badger from "~/pages/settings/roles/roleBadge.vue";
import LogInfo from "./logInfo/index.vue";

const props = defineProps<{
  data: Array<LogData>;
  levelMap: Record<number, string>;
  colors: Record<number, string>;
  loading?: boolean;
}>();
const pagination = defineModel<{
  limit: number;
  page: number;
  total: number;
}>("pagination", {
  required: true,
});
const { $dayjs } = useNuxtApp();

const columns = ref([
  {
    accessorKey: "level",
    header: "Level",
    class: "w-24",
    rowClass: "!px-3 !py-3",
  },
  {
    accessorKey: "timestamp",
    header: "Time",
    class: "w-24",
    rowClass: "!px-3 !py-3",
  },
  {
    accessorKey: "module",
    header: "Module",
    class: "w-48",
    rowClass: "!px-3 !py-3",
  },
  {
    accessorKey: "actor",
    header: "By",
    class: "w-48",
    rowClass: "!px-3 !py-3",
  },
  {
    accessorKey: "action",
    header: "Header",
    class: "",
    rowClass: "!px-3 !py-3",
  },
  {
    accessorKey: "actions",
    header: "Actions",
    class: "w-20",
    rowClass: "!px-3 !py-3",
  },
]);

const modal = ref<{
  show: boolean;
  data?: LogData;
}>({
  show: false,
  data: undefined,
});

const openModal = (data: LogData) => {
  modal.value.data = data;
  modal.value.show = true;
};
</script>

<template>
  <TCard :ui="{ body: 'p-0 sm:p-0' }">
    <template v-if="false" #header>
      <div class="flex items-center gap-2">
        <TInput placeholder="Search" />
      </div>
    </template>
    <TTable :columns :data :loading class="h-full max-h-full">
      <template #level-cell="{ row }">
        <Badger variant="outline" :color="colors[row.original.level]!">
          {{ levelMap[row.original.level] }}
        </Badger>
      </template>

      <template #timestamp-cell="{ row }">
        {{ $dayjs(row.original.timestamp).format("hh:mm:ss A") }}
      </template>

      <template #action-cell="{ row }">
        <div class="line-clamp-2 whitespace-break-spaces">
          {{ row.original.action }}
        </div>
      </template>

      <template #actions-cell="{ row }">
        <TButton
          variant="outline"
          icon="tabler:eye"
          :ui="{ base: 'w-full justify-center' }"
          @click="openModal(row.original)"
        />
      </template>
    </TTable>

    <TModal
      v-model:open="modal.show"
      :ui="{
        content: 'sm:max-w-3xl',
      }"
    >
      <template #content>
        <LogInfo
          :data="modal.data!"
          :levelMap
          :colors
          @close="modal.show = false"
          class="h-screen-90"
        />
      </template>
    </TModal>

    <template #footer>
      <TPagination
        v-model:page="pagination.page"
        :total="pagination.total"
        :pageCount="pagination.limit"
      />
    </template>
  </TCard>
</template>
