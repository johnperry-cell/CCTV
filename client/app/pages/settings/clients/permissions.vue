<script setup lang="ts">
import PermissionSelect from "~/components/userEditor/permissions/permissionsSelect.vue";
import type { Client, PermissionItem } from "~/types/models";

const client = defineModel<Client>({
  required: true,
});
const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();

const permissions = ref<Array<PermissionItem>>(client.value.permissions);
const loading = ref<boolean>(true);
const loadingMessage = ref<string>("Loading...");

const savePermissions = () => {
  loading.value = true;
  $api
    .patch(`client/permissions/${client.value.client_id}`, {
      permissions: permissions.value.map((p) => p.id),
    })
    .then((response) => {
      client.value = response.data.data as Client;
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard
    :ui="{
      root: 'sm:h-screen-75 h-screen-95',
      header: 'flex items-center gap-2 px-3 py-2',
      body: 'overflow-y-auto px-3 py-2',
      footer: 'flex items-center justify-end gap-2 px-3 py-2',
    }"
  >
    <template #header>
      <TTypography class="flex-auto"> Client Permissions </TTypography>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <PermissionSelect
      v-model="permissions"
      v-model:loading="loading"
      v-model:loadingMessage="loadingMessage"
      view-selected
      striped
      class="h-full"
      searchApi="client/permissions"
    />
    <template #footer>
      <TButton label="Save" :disabled="loading" @click="savePermissions" />
      <TButton
        label="Cancel"
        :disabled="loading"
        variant="ghost"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
