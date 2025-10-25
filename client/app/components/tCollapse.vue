<script setup lang="ts">
const open = defineModel<boolean>();

const containerRef = useTemplateRef<HTMLElement>("container");

const transitioning = ref<boolean>(false);
const show = computed(() => transitioning.value || open.value);
const hideOverflow = computed(() => !open.value || transitioning.value);

const transitionend = () => {
  transitioning.value = false;
};

watch(open, () => {
  transitioning.value = true;
});

onMounted(() => {
  nextTick(() => {
    containerRef.value?.addEventListener("transitionend", transitionend);
  });
});

onBeforeUnmount(() => {
  containerRef.value?.removeEventListener("transitionend", transitionend);
});
</script>

<template>
  <div
    ref="container"
    class="grid select-none transition-all duration-300"
    :style="`grid-template-rows: ${open ? '1fr' : '0fr'}`"
  >
    <div
      class="row-[1_/_span_2] select-text"
      :class="{
        'overflow-hidden': hideOverflow,
        'overflow-auto': !hideOverflow,
      }"
    >
      <slot v-if="show"></slot>
    </div>
  </div>
</template>
