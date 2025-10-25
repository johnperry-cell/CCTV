<script setup lang="ts">
import type { User, UserRole } from "~/types/models/users";
import RoleBadge from "@/pages/settings/roles/roleBadge.vue";
const { stringToColour } = useColors();

const props = defineProps<{
  user: User;
  canEdit: boolean;
  canChangeTeam: boolean;
}>();

const emit = defineEmits(["edit", "view", "toggle", "changeTeam"]);

const actions = computed(() => {
  return [
    {
      label: "Edit",
      tooltip: "Edit Profile",
      icon: "tabler:pencil",
      onSelect: () => emit("edit"),
      can: props.canEdit,
    },
    {
      label: "Toggle",
      tooltip: "Toggle Status",
      icon: "tabler:lock",
      onSelect: () => emit("toggle"),
      can: props.canEdit,
    },
    {
      label: "Team",
      tooltip: "Change Team",
      icon: "tabler:users-group",
      onSelect: () => emit("changeTeam"),
      can: props.canChangeTeam,
    },
  ].filter((a) => a.can);
});

const image = computed(() =>
  props.user.profile?.images?.find((i) => i.primary),
);

const userColor = computed(() => {
  return stringToColour(
    props.user.profile?.full_name ?? props.user.email ?? props.user.username,
  );
});
</script>

<template>
  <TCard
    class="w-screen-95 relative max-w-80 overflow-hidden"
    :class="{
      grayscale: !user.active,
    }"
    :ui="{
      header: 'flex items-center px-3 py-2 text-xs sm:px-3 sm:py-2',
      body: 'p-0 sm:p-0',
      footer:
        'flex flex-col justify-center gap-0 px-3 py-2 sm:px-0 dark:bg-neutral-800',
    }"
  >
    <template #header>
      <span class="text-dimmed">Account Verfied:</span>
      <span v-if="user.verified" class="group relative flex-auto font-semibold">
        <span
          class="opacity-100 transition-all delay-150 group-hover:select-none group-hover:opacity-0"
        >
          {{ $dayjs(user.verified).fromNow() }}
        </span>
        <span
          class="absolute inset-x-0 top-0 select-none opacity-0 transition-all delay-150 group-hover:select-text group-hover:opacity-100"
        >
          {{ $dayjs(user.verified).format("YYYY-MM-DD HH:mm:ss") }}
        </span>
      </span>
      <span v-else> No </span>
    </template>
    <div
      class="dark:bg-default relative flex items-center justify-center bg-neutral-100 p-4"
    >
      <div
        v-if="image"
        class="absolute inset-0 blur"
        :style="{
          backgroundImage: `url(${image?.url.lg})`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
      />
      <TAvatar
        size="3xl"
        icon="tabler:user-filled"
        :src="image?.url.lg"
        :alt="`${user.username}_avatar`"
        :ui="{
          root: 'size-24 ring-4',
          icon: 'size-16',
        }"
        :style="`--tw-ring-color: ${userColor};`"
        class="z-10"
      />
    </div>
    <template #footer>
      <div class="text-lg font-semibold">
        {{ user.profile?.full_name ?? user.username }}
      </div>
      <div class="text-dimmed">{{ user.email }}</div>
      <div class="">
        <TDropdownMenu :items="actions">
          <TButton
            icon="tabler:chevron-down"
            label="Menu"
            color="neutral"
            variant="outline"
          />
        </TDropdownMenu>
      </div>
    </template>
  </TCard>
</template>
