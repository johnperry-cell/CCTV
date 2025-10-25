<script setup lang="ts">
import type { RoleItem, User } from "~/types/models";
import type { DropdownMenuItem, TableColumn } from "@nuxt/ui";
import RoleBadge from "@/pages/settings/roles/roleBadge.vue";

const { stringToColour } = useColors();

const props = defineProps<{
  canEdit: boolean;
  canChangeTeam: boolean;
}>();
const users = defineModel<Array<User>>();
const emit = defineEmits(["edit", "view", "toggle", "changeTeam"]);

const columns: Array<TableColumn<User>> = [
  {
    id: "avatar",
  },
  {
    accessorKey: "profile.full_name",
    header: "Full name",
  },
  {
    accessorKey: "email",
    header: "Email",
  },
  {
    accessorKey: "roles",
    header: "Roles",
  },
  {
    accessorKey: "active",
    header: "Status",
  },
  {
    id: "actions",
    header: "Actions",
  },
];

const getDropdownActions = (user: User): Array<DropdownMenuItem> => {
  return [
    {
      label: "Edit",
      icon: "tabler:pencil",
      color: "neutral",
      can: props.canEdit,
      onSelect: () => emit("edit", user),
    },
    {
      label: "View",
      icon: "tabler:eye",
      color: "neutral",
      can: false,
      onSelect: () => emit("view", user),
    },
    {
      label: user.active ? "Disable" : "Enable",
      icon: user.active ? "tabler:toggle-right" : "tabler:toggle-left",
      color: user.active ? "error" : "success",
      can: props.canEdit,
      onSelect: () => emit("toggle", user),
    },
    {
      label: "Change Team",
      icon: "tabler:users",
      color: "neutral",
      can: props.canChangeTeam,
      onSelect: () => emit("changeTeam", user),
    },
  ]
    .filter((a) => a.can)
    .map((a) => ({
      label: a.label,
      icon: a.icon,
      onSelect: a.onSelect,
      color: a.color,
    })) as Array<DropdownMenuItem>;
};

const getRoles = (user: User) => {
  return user.roles as Array<RoleItem>;
};

const getImage = (user: User) => {
  return user.profile?.images?.find((i) => i.primary);
};

const getUserColor = (user: User) => {
  return stringToColour(user.profile?.full_name ?? user.email ?? user.username);
};
</script>

<template>
  <TTable :data="users" :columns>
    <template #avatar-cell="{ row }">
      <TAvatar
        size="3xl"
        icon="tabler:user-filled"
        :src="getImage(row.original)?.url?.lg"
        :alt="`${row.original.username}_avatar`"
        :style="`--tw-ring-color: ${getUserColor(row.original)};`"
      />
    </template>

    <template #roles-cell="{ row }">
      <template v-for="role in getRoles(row.original)" :key="role.id">
        <RoleBadge :label="role.name" variant="subtle" :color="role.color" />
      </template>
    </template>

    <template #active-cell="{ row }">
      <TBadge
        variant="outline"
        color="neutral"
        icon="tabler:circle-filled"
        :label="row.original.active ? 'Active' : 'Inactive'"
        :ui="{
          leadingIcon: row.original.active ? 'text-success' : 'text-error',
        }"
      />
    </template>

    <template #actions-cell="{ row }">
      <TDropdownMenu :items="getDropdownActions(row.original)">
        <TButton icon="tabler:dots-vertical" variant="ghost" color="neutral" />
      </TDropdownMenu>
    </template>
  </TTable>
</template>
