<script setup lang="ts">
import type { MenuOption } from "~/types";
import type { ButtonProps } from "@nuxt/ui";
import ActionButton from "./actionButton.vue";

const props = withDefaults(
  defineProps<{
    expand?: boolean;
    menu: MenuOption;
    noIcon?: boolean;
    color?: ButtonProps["color"];
    variant?: ButtonProps["variant"];
    rounded?: boolean;
  }>(),
  {
    expand: false,
    noIcon: false,
    variant: "ghost",
    color: "neutral",
    rounded: false,
  },
);

const subOpen = ref<boolean>(false);

const toggleSubmenu = () => {
  subOpen.value = !subOpen.value;
};
const action = () => {
  if (!props.menu.to) {
    toggleSubmenu();
  }
  props.menu.action?.();
};

onMounted(() => {
  subOpen.value = !!props.menu.active;
});
</script>

<template>
  <div class="">
    <template v-if="!menu.divider">
      <TButtonGroup
        size="md"
        orientation="horizontal"
        class="w-full shadow-none"
        :ui="{
          rounded: rounded ? 'rounded' : 'rounded-none',
        }"
      >
        <TPopover
          mode="hover"
          class="w-full"
          :disabled="expand"
          :popper="{ placement: 'right' }"
        >
          <TButton
            class="relative gap-4 overflow-hidden border-l border-transparent px-2.5"
            :class="{
              'hover:border-gray-500 dark:hover:border-gray-200': noIcon,
            }"
            :color
            :variant
            size="md"
            :activeClass="`text-primary-500 dark:text-primary-400 hover:text-primary-400 dark:hover:!text-primary-300 ${noIcon ? '!border-primary-400' : ''}`"
            :to="menu.to"
            :active="menu.active"
            block
            @click="action"
          >
            <div
              class="pointer-events-none flex flex-auto items-center gap-4"
              :class="{ 'pl-2': noIcon }"
            >
              <div v-if="!noIcon" class="h-5 w-5 leading-none">
                <TIcon v-if="!!menu.icon" :name="menu.icon" class="h-5 w-5" />
              </div>
              <span
                class="line-clamp-1 flex-auto break-all text-left"
                :class="{ 'pointer-events-none opacity-0': !expand }"
              >
                {{ menu.label }}
              </span>
              <div
                v-if="expand && menu.children?.length! > 0 && !menu.to"
                class="h-5 w-5 leading-none"
              >
                <TIcon
                  name="tabler:chevron-right"
                  class="h-5 w-5 transition-transform"
                  :class="subOpen ? 'rotate-90' : 'rotate-0'"
                />
              </div>
            </div>
          </TButton>
          <template v-if="!expand" #panel>
            <div class="flex flex-col gap-2.5 p-2.5">
              <span class="flex items-center gap-2">
                <TIcon :name="menu.icon!" class="h-5 w-5" /> {{ menu.label }}
              </span>
              <div
                v-if="menu.children?.length! > 0"
                class="ml-2.5 flex flex-col gap-2 border-l border-gray-800/25 dark:border-gray-200/25"
              >
                <template v-for="sub in menu.children" :key="`_${sub.label}_`">
                  <ActionButton
                    :expand="true"
                    :menu="sub"
                    noIcon
                    class="-ml-px"
                  />
                </template>
              </div>
            </div>
          </template>
        </TPopover>
        <TButton
          v-if="expand && menu.children?.length! > 0 && !!menu.to"
          icon="tabler:chevron-right"
          class="relative gap-4 overflow-hidden px-2.5"
          truncate
          :color
          :variant
          size="md"
          :ui="{
            leadingIcon:
              'transition-transform ' + (subOpen ? 'rotate-90' : 'rotate-0'),
          }"
          @click="toggleSubmenu"
        />
      </TButtonGroup>
      <TCollapse
        v-if="expand && menu.children?.length! > 0"
        v-model="subOpen"
        class="pl-5"
      >
        <div
          class="flex flex-col gap-2 border-l border-gray-800/25 dark:border-gray-200/25"
        >
          <template v-for="sub in menu.children" :key="`_${sub.label}_`">
            <ActionButton
              :expand
              :menu="sub"
              noIcon
              class="-ml-px"
              :color
              :variant
            />
          </template>
        </div>
      </TCollapse>
    </template>
    <TSeparator v-else color="neutral" class="px-1.5" />
  </div>
</template>
