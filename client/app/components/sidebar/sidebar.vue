<script setup lang="ts">
import type { MenuOption } from "~/types";
import type { RouteLocationRaw } from "vue-router";
import SBHeader from "./sb/header.vue";
import SBActionButton from "./sb/actionButton.vue";

const $router = useRouter();
const $route = useRoute();

const props = defineProps<{
  expand: boolean;
  menus: Array<MenuOption>;
}>();
const emit = defineEmits(["update:expand"]);

const _menus = computed(() => filterMenu(props.menus));

const filterMenu = (menu: MenuOption[]) => {
  let result: MenuOption[] = [];
  menu.forEach((item) => {
    if (toValue(item.hidden) !== true) {
      let children = null;
      if (!!item.children && item.children.length > 0) {
        children = { children: filterMenu(item.children) };
      }
      result.push(Object.assign({}, item, children));
    }
  });

  return checkActive(cleanupDividers(result));
};

const cleanupDividers = (menu: MenuOption[]) => {
  return menu
    .filter((item, index, arr) => !(item.divider && arr[index + 1]?.divider))
    .filter((m, i, a) => !(m.divider && i === a.length - 1));
};

const checkActive = (menu: MenuOption[]) => {
  let tmp = [...menu];
  tmp.forEach((item) => {
    item.active = isActive(item);
    if (item.children) checkActive(item.children);
  });
  return tmp;
};

const isActive = (item: MenuOption): boolean => {
  let exact = false;
  if (item.to) {
    let r = $router.resolve(item.to as RouteLocationRaw);
    exact = r.name === $route.name;
  }
  return exact || (item.children?.some(isActive) ?? false);
};
</script>

<template>
  <div
    class="fixed inset-0 z-50 transition-all"
    :class="{
      'bg-neutral-800/15 dark:bg-neutral-100/15': expand,
      'pointer-events-none bg-neutral-800/0 dark:bg-neutral-100/0': !expand,
    }"
    @click="$emit('update:expand', false)"
  >
    <aside
      class="bg-default/75 border-default pointer-events-auto relative flex h-screen flex-col gap-[0.625rem] border-r backdrop-blur transition-all"
      :class="{
        'w-72': expand,
        'w-[4.5rem]': !expand,
      }"
      @click.prevent.stop
    >
      <SBHeader
        :expand
        :title="$config.public.product_name"
        @close="$emit('update:expand', false)"
      />
      <div
        class="flex flex-col gap-2 px-4 py-3"
        :class="{
          'overflow-y-auto': expand,
        }"
      >
        <template v-for="menu in _menus" :key="`_${menu.label}_`">
          <SBActionButton :expand :menu />
        </template>
      </div>
      <div
        class="flex flex-auto flex-col gap-2 px-4 py-3"
        :class="{
          'overflow-y-auto': expand,
        }"
      >
        <slot :expand> </slot>
      </div>
      <div
        class="flex flex-col gap-2 px-4 py-3"
        :class="{
          'overflow-y-auto': expand,
        }"
      >
        <slot name="footer" :expand> </slot>
      </div>
    </aside>
  </div>
</template>
