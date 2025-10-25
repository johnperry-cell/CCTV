<script setup lang="ts">
import { z } from "zod";
import type { Form, FormSubmitEvent } from "#ui/types";
import type { PermissionItem } from "~/types/models/permission";

const { $api } = useNuxtApp();
const toast = useToast();

const model = defineModel<PermissionItem>();
const props = defineProps<{ guard?: string }>();

const emit = defineEmits(["close"]);

const pattern = /[^:a-z_-]{1,}/g;
const loading = ref<boolean>(false);
const debounce = ref<NodeJS.Timeout | null>(null);
const schema = z.object({
  name: z
    .string({ message: "Name is required" })
    .min(1, { message: "Name is required" }),
  // .refine((val) => !pattern.test(val), {
  //   message: "Name can only contain letters, underscores, dashes and colons",
  // }),
  description: z.string().optional().nullable(),
  g: z.string().optional().nullable(),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref({
  name: model.value?.name,
  description: model.value?.description,
  g: props.guard,
});

const isEdit = computed(() => !!model.value?.id);

const savePermission = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;

    const method = isEdit.value ? "patch" : "post";
    const uri = `/permissions${isEdit.value ? "/" + model.value?.id : ""}`;

    $api[method](uri, e.data)
      .then((response) => {
        model.value = response.data.data;

        toast.add({
          title: "Success",
          description:
            response.data.message ?? "Permission saved successfully!",
          color: "primary",
          icon: "tabler:circle-check",
        });

        resolve(response);
      })
      .catch((error) => {
        const errors = Object.keys(error.response.data.errors).map((k) => {
          return { name: k, message: error.response.data.errors[k][0] };
        });
        form.value?.setErrors(errors);
        reject(error);
      })
      .finally(() => {
        loading.value = false;
      });
  });
};

const onChange = (e: KeyboardEvent) => {
  if (debounce.value) {
    clearTimeout(debounce.value);
  }

  debounce.value = setTimeout(() => {
    const rawValue = (e.target as HTMLInputElement).value;
    state.value.name = rawValue
      .toLowerCase()
      .replace(/\s/g, "_")
      .replace(pattern, "");
  }, 50);
};
</script>

<template>
  <TCard
    :ui="{
      root: 'relative',
      header: 'flex w-full items-center justify-between px-4 py-2',
      footer: 'justify-end',
    }"
  >
    <template #header>
      <h3 class="text-base font-semibold leading-6">
        {{ isEdit ? "Edit" : "Add" }} Permission
      </h3>
      <TButton
        color="neutral"
        variant="ghost"
        icon="i-heroicons-x-mark-20-solid"
        class="-my-1"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <TForm
      :schema
      :state
      :validateOn="[]"
      ref="form"
      class="space-y-4"
      @submit="savePermission($event)"
    >
      <TFormField label="Name" name="name">
        <TInput
          v-model="state.name"
          placeholder="Enter Permission Name"
          :disabled="loading"
          :ui="{
            base: 'max-w-full',
          }"
        />
      </TFormField>
      <TFormField label="Description" name="description">
        <TTextarea
          v-model="state.description"
          placeholder="Enter Description"
          :disabled="loading"
          :ui="{
            root: 'scrollbar-thin w-full',
          }"
        />
      </TFormField>
    </TForm>

    <TInnerLoading :active="loading" text="Saving permission..." />
    <template #footer>
      <TButton
        label="Submit"
        :disabled="loading"
        :loading
        @click="form?.submit"
      />
      <TButton
        variant="ghost"
        color="neutral"
        label="Cancel"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
