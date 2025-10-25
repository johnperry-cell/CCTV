<script setup lang="ts">
import { TBadge } from "#components";
const { hexAToRGBA } = useColors();
const props = defineProps<{
  color: string;
}>();

const badge = ref<InstanceType<typeof TBadge> | null>(null);

const setColors = () => {
  const rgba = hexAToRGBA(props.color);
  const rgbVar = `${rgba.r} ${rgba.g} ${rgba.b}`;
  const el = badge.value?.$el;

  if (el) {
    el.style.setProperty(
      "--tw-ring-color",
      `rgb(${rgbVar} / var(--tw-ring-opacity))`,
    );
    el.style.color = props.color;
    el.style.backgroundColor = `rgb(${rgbVar} / 0.25)`;
  }
};

watch(() => props.color, setColors);

onMounted(() =>
  nextTick(() => {
    setColors();
  }),
);
</script>

<template>
  <TBadge ref="badge" class="brightness-75 dark:brightness-100">
    <slot />
  </TBadge>
</template>
