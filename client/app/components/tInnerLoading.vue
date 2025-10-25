<script setup lang="ts">
const props = defineProps<{
  active: boolean;
  text?: string;
  icon?: string;
}>();
const appConfig = useAppConfig();

const transitioning = ref(false);
const el = ref<HTMLDivElement>();

const parentEl = ref<HTMLElement>();

const parentStyle = ref<any>();

const style = computed(() => {
  if (parentStyle.value) {
    const _styles = parentStyle.value;
    const tl =
      parseFloat(_styles.borderTopLeftRadius ?? 0) -
      Math.max(
        parseFloat(_styles.borderTopWidth ?? 0),
        parseFloat(_styles.borderLeftWidth ?? 0),
      );

    const tr =
      parseFloat(_styles.borderTopRightRadius ?? 0) -
      Math.max(
        parseFloat(_styles.borderTopWidth ?? 0),
        parseFloat(_styles.borderRightWidth ?? 0),
      );

    const bl =
      parseFloat(_styles.borderBottomLeftRadius ?? 0) -
      Math.max(
        parseFloat(_styles.borderBottomWidth ?? 0),
        parseFloat(_styles.borderLeftWidth ?? 0),
      );

    const br =
      parseFloat(_styles.borderBottomRightRadius ?? 0) -
      Math.max(
        parseFloat(_styles.borderBottomWidth ?? 0),
        parseFloat(_styles.borderRightWidth ?? 0),
      );

    return {
      "border-top-left-radius": `${tl}px`,
      "border-top-right-radius": `${tr}px`,
      "border-bottom-left-radius": `${bl}px`,
      "border-bottom-right-radius": `${br}px`,
    };
  }
  return {};
});

const getNextRelativeParent = (el: HTMLElement) => {
  let parent = el.parentElement;

  while (parent) {
    if (computedStyles(parent).position === "relative") {
      return parent;
    }
    parent = parent.parentElement;
  }

  return window.document.body;
};

const computedStyles = (el: HTMLElement) => {
  return window.getComputedStyle(el);
};

onMounted(() =>
  nextTick(() => {
    parentEl.value = getNextRelativeParent(el.value!);
    parentStyle.value = computedStyles(parentEl.value);
  }),
);
</script>

<template>
  <Transition
    enter-from-class="opacity-0"
    leave-to-class="opacity-0"
    enter-active-class="transition duration-300"
    leave-active-class="transition duration-300"
    @before-leave="transitioning = true"
    @after-enter="transitioning = false"
  >
    <div
      v-show="active"
      ref="el"
      class="trans absolute inset-0 z-20 flex flex-col items-center justify-center gap-2 bg-gray-200/15 drop-shadow-md backdrop-blur-sm dark:bg-gray-100/15"
      :style
    >
      <slot>
        <TIcon
          :name="appConfig.ui.icons.loading"
          class="h-8 w-8 animate-spin"
        />
        <TTypography variant="span">
          {{ text }}
        </TTypography>
      </slot>
    </div>
  </Transition>
</template>
