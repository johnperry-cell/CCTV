<script setup lang="ts">
import type { PermissionItem } from "~/types/models/permission";
import Editor from "./editor.vue";
import Delete from "./delete.vue";
import type { TableColumn } from "@nuxt/ui";

const { merge } = useModels();
const $dayjs = useDayjs();
const $route = useRoute();

type ModalType = "Editor" | "Delete";
const guards = [
  {
    g: undefined,
    label: "Users",
  },
  {
    g: "client",
    label: "API Clients",
  },
];

const abortController = new AbortController();

const { pagination, params, loading, search } = useSearcher<{
  search: string;
  g?: string;
}>(
  computed(() => ({
    api: "/permissions",
    limit: 9,
    appendToUrl: true,
    type: "custom",
    signal: abortController.signal,
    onSearch: (response) => {
      permissions.value = response.data.data as Array<PermissionItem>;
    },
  })),
);

const columns: Array<TableColumn<PermissionItem>> = [
  {
    accessorKey: "id",
    header: "ID",
  },
  {
    accessorKey: "name",
    header: "Name",
  },
  {
    accessorKey: "description",
    header: "Description",
  },
  {
    accessorKey: "date",
    header: "Created At",
  },
  {
    accessorKey: "actions",
    header: "Actions",
  },
];

const permissions = ref<Array<PermissionItem>>([]);
const modal = ref<{
  open: boolean;
  data?: PermissionItem;
  type: ModalType;
}>({
  open: false,
  type: "Editor",
});

const onSave = (data: PermissionItem) => {
  merge(permissions.value, data);
  modal.value.open = false;
};

const openModal = (data?: PermissionItem, type: ModalType = "Editor") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.open = true;
};

const onDelete = (data: PermissionItem) => {
  permissions.value = permissions.value.filter((p) => p.id !== data.id);
  modal.value.open = false;
};

const changeGuard = (g?: string) => {
  params.value.g = g;
  search();
};

onMounted(() => {
  params.value.g = $route.query.g as string;
  search();
});

onBeforeUnmount(() => {
  abortController.abort();
});
</script>

<template>
  <TContainer class="h-full py-4">
    <TCard
      :ui="{
        root: 'h-full divide-y-0',
        header:
          'bg-default border-default sticky top-14 z-20 rounded-t-lg border-b',
        footer:
          'bg-default border-default sticky bottom-0 justify-end rounded-b-lg border-t',
        body: 'p-0 sm:p-0',
      }"
    >
      <template #header>
        <div class="flex flex-auto items-center justify-between">
          <div class="flex items-center gap-4">
            <TInput
              v-model="params.search"
              size="md"
              color="neutral"
              trailing-icon="tabler:search"
              placeholder="Search..."
              class="flex-auto"
              @keyup.enter="search"
            >
              <template #trailing>
                <TButton
                  icon="tabler:search"
                  :loading
                  color="neutral"
                  size="md"
                  variant="link"
                  class="px-3"
                  @click="search()"
                />
              </template>
            </TInput>
            <template v-for="g in guards" :key="g.g">
              <TButton
                :label="g.label"
                :variant="g.g === params.g ? 'solid' : 'ghost'"
                @click="changeGuard(g.g)"
              />
            </template>
          </div>
          <TButton icon="tabler:plus" @click="openModal(undefined, 'Editor')">
            Add Permission
          </TButton>
        </div>
      </template>
      <TTable :columns :data="permissions" :loading>
        <template #date-cell="{ row }">
          {{ $dayjs(row.original.date).format("DD MMM YYYY") }}
        </template>

        <template #actions-cell="{ row }">
          <div class="flex items-center">
            <TButton
              icon="tabler:edit"
              color="neutral"
              size="md"
              variant="ghost"
              @click="openModal(row.original, 'Editor')"
            />
            <TButton
              icon="tabler:trash"
              color="error"
              size="md"
              variant="ghost"
              @click="openModal(row.original, 'Delete')"
            />
          </div>
        </template>

        <template #description-cell="{ row }">
          <span class="inline-block max-w-[400px] text-wrap">
            {{ row.original.description }}
          </span>
        </template>
      </TTable>
      <TInnerLoading :active="loading" text="Fetching permissions..." />
      <template #footer>
        <TPagination
          v-model:page="pagination.page"
          :total="pagination.total"
          :pageCount="pagination.limit"
        />
      </template>
    </TCard>
    <TModal
      v-model:open="modal.open"
      :dismissible="!loading"
      :ui="{ content: 'w-screen-95 max-w-sm sm:max-w-sm' }"
    >
      <template #content="{ close }">
        <Editor
          v-if="modal.type === 'Editor'"
          v-model="modal.data as PermissionItem"
          :guard="modal.data ? undefined : params.g"
          @update:modelValue="onSave($event as PermissionItem)"
          @close="close"
        />

        <Delete
          v-if="modal.type === 'Delete'"
          :modelValue="modal.data!"
          @delete="onDelete"
          @close="close"
        />
      </template>
    </TModal>
  </TContainer>
</template>
