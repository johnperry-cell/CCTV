<script setup lang="ts">
const props = defineProps<{
  preventClose?: boolean;
}>();

const state = defineModel({
  type: Boolean,
});

const closePrevented = ref(false);

const close = (force: boolean = false) => {
  if (!props.preventClose || force) {
    state.value = false;
  } else {
    closePrevented.value = true;
  }
};

const afterShake = (e: AnimationEvent) => {
  closePrevented.value = false;
};

onMounted(() => {});
</script>

<template>
  <div v-if="state" class="absolute inset-0 overflow-hidden rounded">
    <div class="relative flex h-full w-full items-center justify-center">
      <div
        class="absolute inset-0 bg-neutral-400/25 backdrop-blur-sm"
        @click="close(false)"
      />
      <div
        class="absolute mx-auto my-auto max-w-full"
        :class="{
          'animate-shake': closePrevented,
        }"
        @animationend="afterShake"
      >
        <slot :close="() => close(true)"></slot>
      </div>
    </div>
  </div>
</template>
