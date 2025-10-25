<script setup lang="ts">
import { z } from "zod";
import { TForm } from "#components";
import type { Form, FormErrorEvent, FormSubmitEvent } from "#ui/types";
import type { RoleItem } from "~/types/models";
import PermissionSelect from "~/components/userEditor/permissions/permissionsSelect.vue";

const { randomColor } = useColors();

const { $api } = useNuxtApp();
const toast = useToast();
const model = defineModel({
  default: null,
  type: Object as PropType<Partial<RoleItem> | null>,
});

const emit = defineEmits(["close"]);

const loading = ref(false);
const loadingMessage = ref("");
const permissionError = ref<string>();

const schema = z.object({
  name: z
    .string({ message: "Name is required" })
    .min(1, { message: "Name is required" }),
  description: z.string().optional(),
  color: z.string().refine(
    (val) => {
      if (!val) return false;
      return /^#(?:[0-9a-fA-F]{3,4}){1,2}$/i.test(val);
    },
    {
      message: "Color must be a valid hex color code",
    },
  ),
  permissions: z
    .array(
      z.object({
        id: z.string(),
        name: z.string(),
        description: z.string().nullable(),
        date: z.string(),
      }),
    )
    .optional(),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref({
  name: model.value?.name ?? "",
  description: model.value?.description ?? "",
  color: model.value?.color ?? randomColor(),
  permissions: model.value?.permissions ?? [],
});

const isEdit = computed(() => !!model.value?.id);
const showDesc = ref(false);

const saveRole = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;

    const method = isEdit.value ? "patch" : "post";
    const uri = `/roles${isEdit.value ? "/" + model.value?.id : ""}`;

    const data = {
      ...e.data,
      permissions: e.data.permissions?.map((p) => p.id) ?? [],
    };

    $api[method](uri, data)
      .then((response) => {
        model.value = response.data.data;

        toast.add({
          title: "Success",
          description: response.data.message ?? "Role saved successfully!",
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
      })
      .finally(() => {
        loading.value = false;
      });
  });
};

const onError = async (e: FormErrorEvent) => {
  permissionError.value = e.errors.find(
    (err) => err.name === "permissions",
  )?.message;
};
</script>

<template>
  <TCard
    :ui="{
      root: 'max-h-screen-90 relative',
      header: 'flex w-full items-center justify-between px-4 py-2',
      body: 'overflow-y-auto p-0 sm:p-0',
      footer: 'justify-end',
    }"
  >
    <template #header>
      <div class="flex w-full items-center justify-between py-2">
        <h3 class="text-base font-semibold leading-6">
          {{ isEdit ? "Edit" : "Add" }} Role
        </h3>
        <TButton
          color="neutral"
          variant="ghost"
          icon="i-heroicons-x-mark-20-solid"
          class="-my-1"
          :disabled="loading"
          @click="emit('close')"
        />
      </div>
    </template>

    <TForm
      :schema
      :state
      :validateOn="[]"
      ref="form"
      class="flex flex-auto flex-col gap-4 px-4 py-2"
      @submit="saveRole($event)"
      @error="onError"
    >
      <div class="flex items-center gap-4">
        <TFormField
          label="Name"
          name="name"
          :required="!modelValue?.protected"
          class="flex-auto"
        >
          <TInput
            v-model="state.name"
            placeholder="Enter Permission Name"
            autocomplete="off"
            :disabled="loading || modelValue?.protected"
            :ui="{
              base: 'max-w-full',
            }"
          />
          <template v-if="modelValue?.protected" #hint>
            <TTooltip
              text="Name change is disabled for this role"
              :ui="{ content: 'h-auto text-wrap' }"
            >
              <TIcon name="tabler:lock" />
            </TTooltip>
          </template>
        </TFormField>
        <div class="flex items-center gap-1">
          <TFormField label="Color" name="color" required>
            <TColorPicker v-model="state.color" v-slot="{ color, isDark }">
              <TButton
                id="color"
                icon="tabler:color-picker"
                class="w-full flex-wrap justify-center"
                :class="{
                  '!text-neutral-200': isDark(state.color!),
                  '!text-neutral-700': !isDark(state.color!),
                }"
                :disabled="loading"
                :style="{ backgroundColor: state.color }"
              />
            </TColorPicker>
          </TFormField>
          <TButton
            icon="tabler:file-description"
            variant="ghost"
            color="neutral"
            class="self-end"
            :class="{
              'text-primary-400 dark:text-primary-400': showDesc,
            }"
            @click="showDesc = !showDesc"
          />
        </div>
      </div>
      <TCollapse v-model="showDesc">
        <TFormField
          label="Description"
          name="description"
          hint="Optional"
          class="w-full px-0.5"
        >
          <TTextarea
            v-model="state.description"
            placeholder="Enter Description"
            :disabled="loading"
            class="scrollbar-thin mb-0.5 w-full"
          />
        </TFormField>
      </TCollapse>
      <PermissionSelect
        searchApi="/roles/permissions"
        v-model="state.permissions"
        v-model:loading="loading"
        v-model:loadingMessage="loadingMessage"
        view-selected
        :error="permissionError"
        class="flex-auto transition-all"
        :class="{
          '-mt-4': !showDesc,
        }"
      />
    </TForm>
    <TInnerLoading :active="loading" :text="loadingMessage" />
    <template #footer>
      <TButton label="Submit" :disabled="loading" @click="form?.submit" />
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
