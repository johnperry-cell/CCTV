<script setup lang="ts">
import { useFocusTrap } from "@vueuse/integrations/useFocusTrap";
import type { ProfileImage, User } from "~/types/models/users";
const props = defineProps<{
  image: ProfileImage;
  api: string;
}>();
const user = defineModel<User>("user");
const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);
const target = ref();
const { hasFocus, activate, deactivate } = useFocusTrap(target);
const deleteImage = () => {
  loading.value = true;
  $api
    .delete(`${props.api}/${props.image.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Profile Updated",
        description: response.data.message ?? "Image deleted successfully",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};

onMounted(() => {
  nextTick(() => {
    activate();
  });
});

onBeforeUnmount(() => {
  deactivate();
});
</script>

<template>
  <TCard
    ref="target"
    :ui="{
      root: 'sm:w-screen-95 max-w-sm divide-y divide-neutral-400/25',
      header: 'relative flex w-full items-center justify-between px-4 py-2',
      footer: 'flex items-center justify-end gap-2 px-4 py-2',
    }"
  >
    <template #header>
      <div
        class="text-error-500 dark:text-error-400 flex flex-auto flex-col items-center"
      >
        <TIcon name="tabler:alert-triangle" class="size-16" />
        <h3 class="flex-auto text-center text-base font-semibold leading-6">
          Are you sure you want to delete this image?
        </h3>
      </div>
      <span class="absolute right-2 top-2">
        <TButton
          color="neutral"
          variant="ghost"
          icon="i-heroicons-x-mark-20-solid"
          class="-my-1"
          :disabled="loading"
          @click="emit('close')"
        />
      </span>
    </template>
    <div class="flex items-center gap-2 px-8 py-2">
      <TAvatar :src="image.url.lg" size="3xl" />
      <div>
        <TTypography variant="sm">
          This will permanently delete the selected image.
        </TTypography>
        <TTypography variant="sm" class="font-bold">
          This cannot be undone!
        </TTypography>
      </div>
    </div>

    <TInnerLoading :active="loading" text="Deleting address..." />

    <template #footer>
      <TButton
        label="Delete"
        color="error"
        :disabled="loading"
        :loading
        @click="deleteImage"
      />
      <TButton
        label="Cancel"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
