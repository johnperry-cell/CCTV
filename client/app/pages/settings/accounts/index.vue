<script setup lang="ts">
import type { User } from "~/types/models/users";
import type { HasKey } from "~/types";
import UserItem from "./userItem2.vue";
import UsersTable from "./usersTable.vue";
import Editor from "./editor.vue";
import Toggle from "~/components/userEditor/toggle/index.vue";
import TeamChange from "./teamChange/index.vue";

const { merge } = useModels();
const $auth = useAuthStore();
const $guard = useGuard();

const abortController = new AbortController();

const { pagination, params, loading, search } = useSearcher<{
  search: string;
  perms: boolean;
}>({
  api: "/users",
  appendToUrl: true,
  type: "custom",
  signal: abortController.signal,
  onSearch: (response) => {
    users.value = response.data.data as Array<User>;
  },
});
const users = ref<Array<User>>([]);
const modal = ref<{
  show: boolean;
  data?: User;
  type: string;
}>({
  show: false,
  data: undefined,
  type: "Editor",
});
const view = ref("table");
const views = ref([
  {
    icon: "tabler:table",
    label: "Table view",
    value: "table",
  },
  {
    icon: "tabler:cards",
    label: "Card view",
    value: "card",
  },
]);

const viewIcon = computed(
  () => views.value.find((v) => v.value === view.value)?.icon,
);

const modalInfo = computed(() => {
  const info = Object.create({
    Editor: {
      title: "Account Editor",
      description: "Update user account details",
    },
    Toggle: {
      title: "Toggle user status",
      description: "Enable / Disable user account",
    },
    TeamChange: {
      title: "User Team",
      description: "Change user team",
    },
  });

  return info[modal.value.type];
});

const modalWidth = computed(() => {
  const main = "w-screen-95";
  const widths = {
    Editor: "sm:max-w-xl",
    Toggle: "sm:max-w-sm",
  } as HasKey;

  return [main, widths[modal.value.type]].join(" ");
});

const openModal = (data?: User, type: string = "") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};

const onNewUser = (newUser: User) => {
  merge(users.value, newUser);
};

const onTeamChange = (user: User) => {
  users.value = users.value.filter((u) => u.id !== user.id);
  modal.value.show = false;
};

useTeams(() => {
  search();
});

onMounted(() => {
  params.value.perms = true;
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
      }"
    >
      <template #header>
        <div class="flex flex-auto items-center gap-2">
          <div class="flex flex-auto items-center gap-4">
            <TInput
              v-model="params.search"
              size="md"
              color="neutral"
              trailing-icon="tabler:search"
              placeholder="Search..."
              name="search"
              @keyup.enter="search()"
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
            v-if="$guard.can('users_add')"
            icon="tabler:plus"
            @click="openModal(undefined, 'Editor')"
          >
            Add User
          </TButton>
          <TSelect
            v-model="view"
            :items="views"
            :icon="viewIcon"
            class="w-36"
          />
        </div>
      </template>
      <div v-if="view === 'card'" class="flex flex-wrap gap-4">
        <template v-for="user in users" :key="user.id">
          <UserItem
            :canEdit="
              $guard.canAny(
                'users_edit-profile',
                'users_edit-account',
                'users_edit-permission',
              )
            "
            :canChangeTeam="$guard.isSuperAdmin() && $auth.hasTeam"
            :user
            @edit="openModal(user, 'Editor')"
            @toggle="openModal(user, 'Toggle')"
            @changeTeam="openModal(user, 'TeamChange')"
          />
        </template>
      </div>

      <UsersTable
        v-if="view === 'table'"
        v-model="users"
        :canEdit="
          $guard.canAny(
            'users_edit-profile',
            'users_edit-account',
            'users_edit-permission',
          )
        "
        :canChangeTeam="$guard.isSuperAdmin() && $auth.hasTeam"
        @edit="openModal($event, 'Editor')"
        @toggle="openModal($event, 'Toggle')"
        @changeTeam="openModal($event, 'TeamChange')"
      />

      <template #footer>
        <TPagination
          v-model:page="pagination.page"
          :total="pagination.total"
          :pageCount="pagination.limit"
        />
      </template>

      <TModal
        v-model:open="modal.show"
        :dismissible="false"
        :title="modalInfo.title"
        :description="modalInfo.description"
        :ui="{ content: modalWidth }"
      >
        <template #content="{ close }">
          <Editor
            v-if="modal.type === 'Editor'"
            v-model="modal.data"
            @update:modelValue="onNewUser($event! as User)"
            @close="close"
          />
          <Toggle
            v-else-if="modal.type === 'Toggle'"
            v-model:user="modal.data!"
            @update:user="onNewUser($event! as User)"
            @close="close"
          />
          <TeamChange
            v-else-if="modal.type === 'TeamChange'"
            v-model="modal.data!"
            @transfer="onTeamChange($event! as User)"
            @close="close"
          />
        </template>
      </TModal>
    </TCard>
  </TContainer>
</template>
