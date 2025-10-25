<script setup lang="ts">
import type { AvatarOptions } from "~/types";
import theme from "#build/ui/button";
import type { ComponentConfig } from "@nuxt/ui";
import type { AppConfig } from "@nuxt/schema";

import ThemeModes from "@/components/theme/themeModes.vue";

type Button = ComponentConfig<typeof theme, AppConfig, "button">;

const props = withDefaults(
  defineProps<{
    menus?: AvatarOptions[];
    /**
     * @defaultValue 'md'
     */
    size?: Button["variants"]["size"];
  }>(),
  {
    size: "md",
  },
);

const $auth = useAuthStore();

const signingOut = ref(false);
const isOpen = ref(false);

const hasName = computed(
  () => $auth.profile?.first_name && $auth.profile?.last_name,
);
const profile_name = computed(() => {
  if (hasName.value) {
    let first = $auth.profile?.first_name?.split(" ")[0];
    return `${first} ${$auth.profile?.last_name}`;
  } else {
    return $auth.username;
  }
});

const profile_name_short = computed(() => {
  if (hasName.value) {
    let first = $auth.profile?.first_name?.split(" ")[0];
    return `${first?.[0]}${$auth.profile?.last_name?.[0]}`;
  } else {
    return $auth.username?.[0];
  }
});

const avatar = computed(() => $auth.profile?.images?.find((i) => i.primary));

const _menus = computed<AvatarOptions[]>(
  () => props.menus?.filter((m) => toValue(m.hidden) !== true) || [],
);

const close = () => {
  isOpen.value = false;
};

const logout = () => {
  signingOut.value = true;
  $auth.logout().finally(() => {
    signingOut.value = false;
  });
};
</script>
<template>
  <TPopover v-model:open="isOpen" arrow>
    <TButton
      variant="ghost"
      color="neutral"
      size="lg"
      square
      class="seelct-none p-0"
      :ui="{ base: 'rounded-full' }"
    >
      <TAvatar
        :text="profile_name_short"
        :src="avatar?.url.md"
        size="lg"
        :ui="{ fallback: isOpen ? 'text-primary' : 'text-muted' }"
      />
    </TButton>
    <template #content>
      <div class="flex items-center gap-2 p-4">
        <TAvatar
          :text="profile_name_short"
          :src="avatar?.url.md"
          size="lg"
          class="bg-neutral-200 dark:bg-neutral-600"
        />
        <div class="grid">
          <span class=" ">
            {{ profile_name }}
          </span>
          <span class="text-muted text-xs">
            {{ profile_name }}
          </span>
        </div>
      </div>
      <TSeparator />
      <template v-if="_menus?.length! > 0">
        <div class="flex flex-col gap-2 p-2">
          <template v-for="menu in _menus" :key="menu">
            <template v-if="menu.divider">
              <TSeparator />
            </template>
            <TButton
              v-else-if="Object.values(menu).some((v) => !!v)"
              :icon="menu.icon"
              :label="menu.label"
              :to="menu.to"
              color="neutral"
              :size
              variant="ghost"
              @click="(menu.action?.(), close())"
            />
          </template>
        </div>
      </template>
      <TSeparator />
      <ThemeModes class="p-2" />
      <template v-if="$auth.isLoggedIn">
        <TSeparator />
        <div class="flex flex-col gap-2 p-2">
          <TButton
            icon="tabler:logout"
            label="Logout"
            variant="ghost"
            color="error"
            :size
            :loading="signingOut"
            @click="(logout(), close())"
          />
        </div>
      </template>
      <template v-if="$auth.hasTeam">
        <TSeparator />
        <TTooltip :text="$auth.team?.name">
          <div class="w-full truncate text-center text-xs">
            {{ $auth.team?.name }}
          </div>
        </TTooltip>
      </template>
    </template>
  </TPopover>
</template>
