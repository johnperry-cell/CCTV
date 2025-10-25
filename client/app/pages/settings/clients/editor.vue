<script setup lang="ts">
import { z } from "zod";
import type { Form, FormSubmitEvent } from "#ui/types";
import type { Client } from "@/types/models";
import SecretPreview from "./secretPreview.vue";

const client = defineModel<Client>();
const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref<boolean>(false);

const schema = z.object({
  name: z
    .string({ message: "Client name is required" })
    .min(1, { message: "Client name is required" }),
  domain: z
    .string({ message: "Client domain is required" })
    .min(1, { message: "Client domain is required" })
    .url("Must be a valid url"),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref({
  name: client.value?.client_name,
  domain: client.value?.client_domain,
});

const isEdit = computed(() => !!client.value?.client_id);

const saveClient = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;

    const method = isEdit.value ? "patch" : "post";
    $api[method](
      `/client${isEdit.value ? "/update/" + client.value?.client_id : ""}`,
      e.data,
    )
      .then((response) => {
        client.value = {
          ...response.data.data,
          client_secret: response.data.secret,
        } as Client;

        toast.add({
          title: "Success",
          description: response.data.message ?? "Client saved successfully!",
          color: "primary",
          icon: "tabler:circle-check",
        });
      })
      .catch((error) => {
        const errors = Object.keys(error.response.data.errors).map((k) => {
          return { path: k, message: error.response.data.errors[k][0] };
        });
        form.value?.setErrors(errors);
      })
      .finally(() => {
        loading.value = false;
      });
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
      <TTypography class="flex-auto">
        {{
          `${isEdit ? (client?.client_secret ? "" : "Update") : "Create"} API Client`
        }}
      </TTypography>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>

    <TForm
      v-if="!client?.client_secret"
      :state
      :schema
      :validateOn="[]"
      ref="form"
      class="space-y-4 px-3 py-4"
      @submit="saveClient($event)"
    >
      <TFormField required name="name" label="Client Name">
        <TInput
          v-model="state.name"
          :disabled="loading"
          @keyup.enter="form?.submit()"
        />
      </TFormField>

      <TFormField required name="domain" label="Client Domain">
        <TInput
          v-model="state.domain"
          :disabled="loading"
          @keyup.enter="form?.submit()"
        />
      </TFormField>
    </TForm>

    <SecretPreview v-else :client />

    <TInnerLoading :active="loading" text="Saving client info..." />
    <template #footer>
      <TButton
        v-if="!client?.client_secret"
        label="Save"
        color="primary"
        :disabled="loading"
        @click="form?.submit()"
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
