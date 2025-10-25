<script setup lang="ts">
const router = useRouter();

const canGoBack = computed(() => !!router.currentRoute.value.redirectedFrom);

const props = defineProps<{
  statusCode?: string;
  icon?: string;
  title: string;
  message: string;
}>();
</script>

<template>
  <div class="flex flex-col items-center space-y-5 lg:-mt-64">
    <div class="flex flex-col items-center gap-3">
      <TTypography variant="h1" class="text-8xl font-semibold">
        <TIcon v-if="icon" :name="icon" class="text-error-500" />
        {{ statusCode }}
      </TTypography>
      <TTypography variant="xl" class="font-medium">{{ title }}</TTypography>
      <slot :message>
        <TTypography variant="md" class="max-w-lg text-center">
          {{ message }}
        </TTypography>
      </slot>
    </div>
    <slot name="actions">
      <TButton :label="canGoBack ? 'Go back' : 'Home'" :to="{ name: 'home' }" />
      <TButton v-if="canGoBack" label="Go back'" @click="router.back()" />
    </slot>
    <Ricked class="fixed m-auto !mt-4 text-[1px]" />
  </div>
</template>
