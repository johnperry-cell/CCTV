<script setup lang="ts">
import { type ZodSchema, z } from "zod";
import type { User } from "~/types/models/users";
import type { PasswordGenerateOptions } from "~/types/composables/usePassword";
import type { Form, FormSubmitEvent } from "#ui/types";

const props = defineProps<{
  api: string;
}>();

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const config = useAppConfig().mitd.password.rules;
const { $api } = useNuxtApp();
const toast = useToast();
const { strength, passwordRules, generator } = usePassword();

const schema = z.object({
  password: passwordRules(),
});
type Schema = z.output<typeof schema>;

const passConfig = ref<Partial<PasswordGenerateOptions>>({
  length: config.max
    ? Math.floor(Math.random() * (config.max - config.min + 1))
    : config.min,
  letters: config.letters,
  mixedCase: config.mixedCase,
  numbers: config.numbers,
  symbols: config.symbols,
  exclude: "",
  excludeSimilarCharacters: false,
  excludeSimilarCharactersThreshold: 3,
  excludeSimilarCharactersExclude: "oO0iIl1",
});
const loading = ref(false);
const form = ref<Form<Schema>>();
const state = ref<{
  password: string;
}>({
  password: user.value?.password ?? "",
});

const canGenerate = computed(() => {
  return !["letters", "numbers", "symbols"].every((k) => {
    return !passConfig.value[k];
  });
});
const { generating, generate } = generator({
  onGenerate: (value: string) => (state.value.password = value),
  options: passConfig.value,
});

const onPasswordChange = (e: KeyboardEvent) => {
  setTimeout(() => {
    state.value.password = (e.target as HTMLInputElement).value.replace(
      /\s+/g,
      "",
    );
  }, 5);
};

const onSave = async (e: FormSubmitEvent<Schema>) => {
  loading.value = true;
  return new Promise((resolve) => {
    $api
      .patch(props.api, e.data)
      .then((response) => {
        user.value = response.data.data as User;
        toast.add({
          title: "Success",
          description: response.data.message ?? "Password changed successfully",
          color: "primary",
          icon: "tabler:circle-check",
        });
        state.value.password = "";
        resolve(response);
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
  <TForm
    ref="form"
    :state
    :schema
    :validateOn="[]"
    class="flex flex-col gap-5"
    @submit="onSave($event)"
  >
    <TFormField
      label="Password"
      name="password"
      required
      :ui="{
        wrapper: 'flex-auto',
        container: 'relative flex items-center gap-2',
      }"
      :hint="state.password ? strength(state.password.trim()).complexity : ''"
    >
      <TInput
        v-model="state.password"
        @input="onPasswordChange"
        class="flex-auto"
        placeholder="Enter new password"
        :disabled="loading"
        :ui="{ base: 'max-w-full' }"
      >
        <template #trailing>
          <span
            class="h-3 w-3 rounded-full"
            :style="{
              backgroundColor: strength(state.password.trim()).color,
            }"
          />
        </template>
      </TInput>

      <TButton
        icon="tabler:circle-key"
        color="neutral"
        variant="ghost"
        :disabled="generating || loading || !canGenerate"
        @click="generate"
      />
      <TButton
        :label="loading ? '' : 'Save'"
        type="submit"
        class="w-14 justify-center"
        variant="outline"
        :color="!state.password ? 'neutral' : 'primary'"
        :loading
        :disabled="!state.password"
      />
    </TFormField>
    <div
      class="grid grid-cols-2 items-center gap-4 rounded-md border border-neutral-400/25 bg-neutral-100 px-3 py-2 dark:bg-neutral-900"
    >
      <TTypography variant="span" class="-mb-3 font-semibold">
        Password Generator Config
      </TTypography>
      <span class="col-span-full flex items-center gap-2">
        {{ passConfig.length }}
        <TSlider
          v-model="passConfig.length"
          size="sm"
          :min="config.min"
          :max="config.max ?? 32"
        />
      </span>
      <TCheckbox
        v-model="passConfig.letters"
        label="Include letters"
        :disabled="config.letters || loading || generating"
      />
      <TCheckbox
        v-model="passConfig.mixedCase"
        label="Include mixed case"
        :disabled="config.mixedCase || loading || generating"
      />
      <TCheckbox
        v-model="passConfig.numbers"
        label="Include numbers"
        :disabled="config.numbers || loading || generating"
      />
      <TCheckbox
        v-model="passConfig.symbols"
        label="Include symbols"
        :disabled="config.symbols || loading || generating"
      />
    </div>
  </TForm>
</template>
