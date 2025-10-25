<script setup lang="ts">
import { useFocusTrap } from "@vueuse/integrations/useFocusTrap";
import type { User, Address } from "~/types/models/users";

const { $api } = useNuxtApp();
const toast = useToast();

const props = defineProps<{
  address: Address;
  api: string;
}>();

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const emit = defineEmits(["close"]);

const target = ref();
const loading = ref(false);

const { hasFocus, activate, deactivate } = useFocusTrap(target);

const deleteAddress = () => {
  loading.value = true;

  $api
    .delete(`${props.api}/${props.address.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Success",
        description: response.data.message ?? "Address deleted successfully",
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
      base: 'w-screen-95 max-w-sm',
      ring: '',
      divide: 'divide-y divide-gray-400/25',
      header: {
        base: 'relative flex w-full items-center justify-between',
        padding: 'px-4 py-2',
      },
      footer: {
        base: 'flex items-center justify-end gap-2',
        padding: 'px-4 py-2',
      },
    }"
  >
    <template #header>
      <div
        class="text-sunset-500 dark:text-sunset-400 flex flex-auto flex-col items-center"
      >
        <TIcon name="tabler:alert-triangle" class="h-16 w-16" />
        <h3 class="flex-auto text-center text-base font-semibold leading-6">
          Are you sure you want to delete this address?
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
        This will permanently delete the selected address: <br />
        <div
          class="bg-coral-100 my-3 rounded p-1 font-bold dark:bg-gray-400/30"
        >
          {{ address.full }}
        </div>
      </TTypography>
      <TTypography variant="sm" class="font-semibold">
        This cannot be undone!
      </TTypography>
    </div>

    <TInnerLoading :active="loading" text="Deleting address..." />

    <template #footer>
      <TButton
        label="Delete"
        color="sunset"
        :disabled="loading"
        :loading
        @click="deleteAddress"
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
