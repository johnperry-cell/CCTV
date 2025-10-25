<script setup lang="ts">
import type { Client } from "@/types/models";
import SecretPreview from "./secretPreview.vue";

const client = defineModel<Client>({
  required: true,
});
const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref<boolean>(false);

const regenerate = () => {
  loading.value = true;
  $api
    .patch(`/client/regenerate/${client.value.client_id}`)
    .then((response) => {
      client.value = {
        ...response.data.data,
        client_secret: response.data.secret,
      } as Client;

      toast.add({
        title: "Success",
        description:
          response.data.message ?? "Secret regenerated successfully! ",
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
      body: 'min-h-44 px-3 py-2',
      footer: 'flex items-center justify-end gap-2 px-3 py-2',
    }"
  >
    <template #header>
      <TTypography
        v-if="!client.client_secret"
        variant="lg"
        class="text-error-600 flex flex-auto items-center gap-2"
      >
        <TIcon name="tabler:alert-triangle" class="h-7 w-7" />
        Regenerate this Secret Token?
      </TTypography>
      <TTypography
        v-else
        variant="lg"
        class="text-warning-600 flex flex-auto items-center gap-2"
      >
        <TIcon name="tabler:alert-triangle" class="h-7 w-7" />
        New secret generated!
      </TTypography>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <TTypography
      v-if="!client.client_secret"
      variant="lg"
      class="px-3 text-justify"
    >
      Are you certain you want to regenerate this secret? This action cannot be
      undone. Any integrations that use this secret will not work unless they
      are updated to the new secret by a developer.
    </TTypography>
    <SecretPreview v-else :client />
    <TInnerLoading :active="loading" text="Regenerating secret..." />
    <template #footer>
      <TButton
        v-if="!client.client_secret"
        label="Regenerate"
        :disabled="loading"
        @click="regenerate"
      />
      <TButton
        label="Cancel"
        variant="ghost"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
