<script setup lang="ts">
import type { Client } from "@/types/models";

const client = defineModel<Client>({
  required: true,
});
const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref<boolean>(false);

const toggle = () => {
  loading.value = true;
  $api
    .delete(`client/${client.value.client_id}`)
    .then((response) => {
      client.value = response.data.data as Client;
      toast.add({
        title: "Success",
        description: "Client deleted successfully!",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard
    :ui="{
      header: 'flex items-center gap-2 px-3 py-2',
      body: 'px-3 py-2',
      footer: 'flex items-center justify-end gap-2 px-3 py-2',
    }"
  >
    <template #header>
      <TTypography class="flex-auto">
        {{ `${client.active ? "Disable" : "Enable"} API Client?` }}
      </TTypography>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    Are you sure you want to
    {{ client.active ? "disable" : "enable" }} this API Client?
    {{ client?.active ? "Disabling" : "Enabling" }} this Client will
    {{ client?.active ? "prevent" : "allow" }} it from accessing the API.

    <template #footer>
      <TButton
        v-if="!client.client_secret"
        label="Yes"
        :disabled="loading"
        @click="toggle"
      />
      <TButton
        label="Cancel"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <TInnerLoading :active="loading" text="Toggling client status..." />
  </TCard>
</template>
