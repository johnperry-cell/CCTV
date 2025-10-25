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
      action: () => emit("edit"),
      can: props.canEdit,
    },
    {
      label: "Toggle",
      tooltip: "Toggle Status",
      icon: "tabler:lock",
      action: () => emit("toggle"),
      can: props.canEdit,
    },
    {
      label: "Team",
      tooltip: "Change Team",
      icon: "tabler:users-group",
      action: () => emit("changeTeam"),
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
  >
    <div
      class="absolute inset-x-0 top-0 h-16 opacity-70"
      :style="{ backgroundColor: userColor }"
    />
    <div class="flex h-full flex-col items-center gap-4 p-4">
      <div class="flex flex-col items-center gap-4">
        <TAvatar
          class="shadow-md"
          size="3xl"
          icon="tabler:user-filled"
          :src="image?.url.lg"
          alt="Avatar"
        />
        <TTypography class="text-base">
          {{ user.profile?.full_name ?? user.email ?? user.username }}
        </TTypography>
      </div>
      <!-- Badge Color should change base on color -->
      <div class="text flex flex-auto flex-wrap justify-center gap-1">
        <template v-for="role in user.roles" :key="role.id">
          <RoleBadge
            :color="(role as UserRole).color ?? '#999'"
            :label="(role as UserRole).name"
            size="sm"
            variant="subtle"
            :ui="{ rounded: 'rounded-full' }"
          />
        </template>
      </div>

      <div class="flex flex-wrap items-center justify-center gap-3">
        <template v-for="action in actions" :key="action.label">
          <TTooltip :text="action.tooltip">
            <TButton
              color="neutral"
              size="sm"
              variant="solid"
              @click="action.action"
              class="inline-flex w-20 flex-col items-center justify-center gap-1"
            >
              <TIcon :name="action.icon" class="size-7" />
              <TTypography variant="xs"> {{ action.label }} </TTypography>
            </TButton>
          </TTooltip>
        </template>
      </div>
    </div>
  </TCard>
</template>
