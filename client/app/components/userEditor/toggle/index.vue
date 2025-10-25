<script setup lang="ts">
import type { User } from "~/types/models/users";

const user = defineModel("user", {
  type: Object as PropType<User>,
  default: null,
});

const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);

const image = computed(() =>
  user.value.profile?.images?.find((i) => i.primary),
);

const toggleUser = () => {
  loading.value = true;
  $api
    .delete(`/user/toggle/${user.value.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Success",
        description:
          response.data.message ??
          `User ${user.value.active ? "enabled" : "disabled"} successfully`,
        color: "primary",
        icon: "tabler:circle-check",
      });
      emit("close");
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>
<template>
  <TCard
    ref="target"
    :ui="{
      root: 'w-screen-95 max-w-sm',
      header: 'relative flex w-full items-center justify-between px-4 py-2',
      footer: 'flex items-center justify-end gap-2 px-4 py-2',
    }"
  >
    <template #header>
      <div
        class="flex flex-auto flex-col items-center"
        :class="{
          'text-success-500 dark:text-success-400': !user.active,
          'text-error-500 dark:text-error-400': user.active,
        }"
      >
        <TIcon name="tabler:alert-triangle" class="size-16" />
        <h3 class="flex-auto text-center text-base font-semibold leading-6">
          Are you sure you want to
          {{ user.active ? "disable" : "enable" }} this user?
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
    <div class="flex flex-col gap-2 px-5 py-2 text-center">
      <TTypography variant="sm" class="font-medium">
        This will {{ user?.active ? "prevent" : "allow" }} the user
        {{ user?.active ? "from logging in" : "to login" }}: <br />

        <div
          class="my-3 flex items-center gap-4 rounded bg-neutral-100 p-1 font-bold dark:bg-neutral-400/25"
        >
          <TAvatar
            size="3xl"
            icon="tabler:user-filled"
            alt="Avatar"
            class="bg-neutral-100 shadow-md dark:bg-neutral-700"
            :src="image?.url.lg"
          />
          <div class="flex flex-col text-start">
            <TTypography variant="h6">
              {{ user?.profile?.full_name }}
            </TTypography>
            <TTypography variant="sm">
              {{ user?.username }}
              <span v-if="user?.email" class="text-neutral-500">
                ({{ user?.email }})
              </span>
            </TTypography>
          </div>
        </div>
      </TTypography>
    </div>

    <TInnerLoading :active="loading" text="Deleting address..." />

    <template #footer>
      <TButton
        :label="user.active ? 'Disable' : 'Enable'"
        :color="user.active ? 'error' : 'success'"
        :disabled="loading"
        :loading
        @click="toggleUser"
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
