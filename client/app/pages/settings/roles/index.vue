<script setup lang="ts">
import type { RoleItem } from "~/types/models/roles";
import RoleBadge from "./roleBadge.vue";

import Editor from "./editor.vue";
import Delete from "./delete.vue";
import type { TableColumn } from "@nuxt/ui";

const { merge } = useModels();
const $dayjs = useDayjs();
const $guard = useGuard();

type ModalType = "Editor" | "Delete";
const abortController = new AbortController();

const { pagination, params, loading, search } = useSearcher<{ search: string }>(
  {
    api: "/roles",
    limit: 9,
    appendToUrl: true,
    type: "custom",
    signal: abortController.signal,
    onSearch: (response) => {
      roles.value = response.data.data as Array<RoleItem>;
    },
  },
);

const roles = ref<Array<RoleItem>>([]);
const modal = ref<{
  show: boolean;
  data: Partial<RoleItem> | null;
  type: ModalType;
}>({
  show: false,
  data: null,
  type: "Editor",
});

// const columns = computed(() => {
//   let cols = [
//     {
//       key: "id",
//       label: "ID",
//     },
//     {
//       key: "name",
//       label: "Name",
//     },
//     {
//       key: "description",
//       label: "Description",
//     },
//     {
//       key: "date",
//       label: "Created At",
//     },
//   ];
//   if ($guard.canAny("roles_edit", "roles_add", "roles_delete")) {
//     cols.push({
//       key: "actions",
//       label: "Actions",
//     });
//   }

//   return cols;
// });

const getColumns = (): Array<TableColumn<RoleItem>> => {
  const cols = [
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
  ];
  if ($guard.canAny("roles_edit", "roles_add", "roles_delete")) {
    cols.push({
      accessorKey: "actions",
      header: "Actions",
    });
  }

  return cols;
};

const openModal = (
  data: RoleItem | null = null,
  type: ModalType = "Editor",
) => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};

const getActions = (
  role: RoleItem,
): Array<{
  label: string;
  icon: string;
  click: () => void;
  disabled?: boolean;
}> => {
  let acts = [];
  if ($guard.can("roles_add")) {
    acts.push({
      label: "Duplicate",
      icon: "tabler:copy",
      click: () => duplicateRole(role),
    });
  }

  if ($guard.can("roles_edit")) {
    acts.push({
      label: "Edit",
      icon: "tabler:edit",
      click: () => openModal(role, "Editor"),
    });
  }

  if ($guard.can("roles_delete")) {
    acts.push({
      label: "Delete",
      icon: "tabler:trash",
      disabled: role.protected,
      click: () => openModal(role, "Delete"),
    });
  }
  return acts;
};
const duplicateRole = (data: RoleItem) => {
  modal.value.data = {
    ...data,
    id: undefined,
    name: `${data.name} (copy)`,
    color: undefined,
    protected: false,
  };
  modal.value.type = "Editor";
  modal.value.show = true;
};

const onSave = (data: RoleItem) => {
  merge(roles.value, data);
  modal.value.show = false;
};

const onDelete = (data: RoleItem) => {
  roles.value = roles.value.filter((p) => p.id !== data.id);
  modal.value.show = false;
};

useTeams(search);

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
              name="search"
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
            v-if="$guard.can('roles_add')"
            icon="tabler:plus"
            @click="openModal(null, 'Editor')"
          >
            Add Role
          </TButton>
        </div>
      </template>

      <TTable :columns="getColumns()" :data="roles" :loading>
        <template #name-cell="{ row }">
          <RoleBadge
            :label="row.original.name"
            size="sm"
            variant="subtle"
            :color="row.original.color"
          />
        </template>
        <template #date-cell="{ row }">
          {{ $dayjs(row.original.date).format("DD MMM YYYY") }}
        </template>

        <template #actions-cell="{ row }">
          <div
            v-if="getActions(row.original).length > 0"
            class="flex items-center gap-2"
          >
            <TButton
              v-for="act in getActions(row.original)"
              :key="act.label"
              :icon="act.icon"
              :disabled="act.disabled"
              size="md"
              color="neutral"
              variant="ghost"
              @click="act.click()"
            />
          </div>
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
      v-if="$guard.canAny('roles_add', 'roles_edit', 'roles_delete')"
      v-model:open="modal.show"
      :dismissible="false"
      :ui="{
        content: `w-screen-95 ${modal.type === 'Delete' ? 'sm:max-w-sm' : 'sm:max-w-xl'}`,
      }"
    >
      <template #content="{ close }">
        <Editor
          v-if="
            modal.type === 'Editor' && $guard.canAny('roles_add', 'roles_edit')
          "
          v-model="modal.data"
          @update:modelValue="onSave($event as RoleItem)"
          @close="close"
        />

        <Delete
          v-if="modal.type === 'Delete' && $guard.can('roles_delete')"
          :modelValue="modal.data!"
          @delete="onDelete"
          @close="close"
        />
      </template>
    </TModal>
  </TContainer>
</template>
