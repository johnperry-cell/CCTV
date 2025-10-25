<script setup lang="ts">
import type { AvatarOptions } from "~/types";
import ThemePicker from "@/components/theme/themePicker.vue";

const props = withDefaults(
  defineProps<{
    fixed?: boolean;
    themeToggle?: boolean;
    screenToggle?: boolean;
    avatarOptions?: Array<AvatarOptions>;
    pwaInstall?: boolean;
  }>(),
  {
    fixed: false,
    themeToggle: true,
    screenToggle: true,
    pwaInstall: true,
  },
);
const emit = defineEmits(["toggleSidebar"]);
const $system = useSystemStore();
const $guard = useGuard();

const hidden = ref(false);

onMounted(() => {
  $system.sidebar.collapsed = false;
});
</script>

<template>
  <nav
    class="border-default bg-default/75 z-30 flex w-full items-center border-b backdrop-blur transition-all"
    :class="{
      'sticky top-0': fixed && !hidden,
      '-top-[73px]': hidden,
    }"
  >
    <div
      class="relative flex flex-auto items-center justify-between gap-1.5 px-6 py-3"
    >
      <slot name="prepend"> </slot>
      <div class="flex flex-auto items-center gap-1.5">
        <slot></slot>
      </div>

      <slot name="append">
        <div class="flex items-center gap-1.5">
          <PwaInstall v-if="pwaInstall" />
          <ThemePicker v-if="$guard.isSuperAdmin()" />
          <ScreenToggle v-if="screenToggle" />
          <Avatar v-if="avatarOptions" :menus="avatarOptions" />
        </div>
      </slot>
      <div
        v-if="false"
        class="absolute top-full left-1/2 flex -translate-x-1/2 items-center justify-center transition-all"
        :class="{
          '-translate-y-1/2': !hidden,
        }"
      >
        <TButton
          icon="tabler:square-rounded-chevron-up-filled"
          variant="ghost"
          color="neutral"
          :padded="false"
          :ui="{
            leadingIcon: `transition-transform ${hidden ? 'rotate-180' : ''}`,
          }"
          @click="hidden = !hidden"
        />
      </div>
    </div>
  </nav>
</template>
