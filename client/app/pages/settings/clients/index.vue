<script setup lang="ts">
import { h } from "vue";
import type { Client } from "@/types/models";
import Editor from "./editor.vue";
import Regenerator from "./regenerate.vue";
import Delete from "./delete.vue";
import Permissions from "./permissions.vue";
import type { TableColumn } from "@nuxt/ui";

const { merge } = useModels();
const TButton = resolveComponent("TButton");
const TDropdownMenu = resolveComponent("TDropdownMenu");

const TBadge = resolveComponent("TBadge");

const clients = ref<Array<Client>>([]);

const columns: Array<TableColumn<Client>> = [
  {
    accessorKey: "client_name",
    header: "Name",
  },
  {
    accessorKey: "client_id",
    header: "ID",
  },
  {
    accessorKey: "client_domain",
    header: "Domain",
  },
  {
    accessorKey: "active",
    header: "Active",
    cell: ({ row }) => {
      return h(TBadge, {
        color: row.original.active ? "success" : "error",
        label: row.original.active ? "Active" : "Inactive",
      });
    },
  },
  {
    accessorKey: "actions",
    header: "Actions",
    cell: ({ row }) => {
      const actionBtn = h(TButton, {
        icon: "tabler:dots",
        variant: "ghost",
        color: "neutral",
      });
      return h(
        TDropdownMenu,
        {
          items: [
            {
              type: "separator",
            },
            {
              label: "Edit",
              icon: "tabler:edit",
              onClick: () => openModal(row.original, "Editor"),
            },
            {
              label: row.original.active ? "Disable" : "Enable",
              color: row.original.active ? "error" : "success",
              icon: row.original.active
                ? "tabler:toggle-right"
                : "tabler:toggle-left",
              onClick: () => openModal(row.original, "Delete"),
            },
            {
              label: "Regenerate Secret",
              icon: "tabler:refresh",
              onClick: () => openModal(row.original, "Regenerate"),
            },
            {
              label: "Permissions",
              icon: "tabler:users",
              onClick: () => openModal(row.original, "Permissions"),
            },
          ],
        },
        () => [actionBtn],
      );
    },
  },
];

const modal = ref<{
  show: boolean;
  data?: Client;
  type: string;
}>({
  show: false,
  type: "Editor",
});

const modalInfo = computed(() => {
  const isEdit = !!modal.value.data?.id;
  const info = Object.create({
    Editor: {
      title: isEdit ? "Edit client" : "Create client",
      description: isEdit ? "Edit client details" : "Create new client",
    },
    Regenerate: {
      title: "Regenerate secret",
      description: "Regenerate client secret",
    },
    Delete: {
      title: "Toggle client status",
      description: "Enable / Disable client",
    },
    Permissions: {
      title: "Update client permissions",
      description: "Change client permissions",
    },
  });

  return info[modal.value.type];
});

const abortController = new AbortController();

const { pagination, params, loading, search } = useSearcher<{ search: string }>(
  {
    api: "/client",
    appendToUrl: true,
    type: "custom",
    signal: abortController.signal,
    onSearch: (response) => {
      clients.value = response.data.data;
    },
  },
);

const openModal = (data?: Client, type: string = "Editor") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};

const onUpdate = (data: Client) => {
  const secret = data.client_secret;
  merge(
    clients.value,
    {
      ...data,
      client_secret: undefined,
    },
    true,
    "client_id",
  );
  if (!secret) {
    modal.value.show = false;
  }
};

onMounted(() => {
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
          </div>

          <TButton
            label="Add Client"
            icon="tabler:plus"
            @click="openModal(undefined, 'Editor')"
          />
        </div>
      </template>

      <TTable
        :data="clients"
        :columns="columns"
        sticky
        :loading
        :ui="{ root: 'h-full font-mono font-semibold' }"
      />
      <template v-if="false" #footer>
        <TPagination
          v-model:page="pagination.page"
          :total="pagination.total"
          :pageCount="pagination.limit"
        />
      </template>
    </TCard>

    <TModal
      v-model:open="modal.show"
      :dismissible="false"
      :title="modalInfo.title"
      :description="modalInfo.description"
    >
      <template #content="{ close }">
        <Editor
          v-if="modal.type === 'Editor'"
          v-model="modal.data"
          @close="close"
          @update:modelValue="onUpdate($event as Client)"
        />
        <Regenerator
          v-else-if="modal.type === 'Regenerate'"
          v-model="modal.data!"
          @close="close"
        />
        <Delete
          v-else-if="modal.type === 'Delete'"
          v-model="modal.data!"
          @close="close"
          @update:modelValue="onUpdate"
        />
        <Permissions
          v-else-if="modal.type === 'Permissions'"
          v-model="modal.data!"
          @close="close"
          @update:modelValue="onUpdate"
        />
      </template>
    </TModal>
  </TContainer>
</template>
