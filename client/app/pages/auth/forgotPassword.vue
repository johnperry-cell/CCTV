<script setup lang="ts">
import { z } from "zod";
import type { FormSubmitEvent } from "#ui/types";

const { $api } = useNuxtApp();

const schema = z.object({
  email: z
    .string({
      message: "Email address is required!",
    })
    .min(1, {
      message: "Email address is required!",
    })
    .email("Must be a valid email"),
});

type Schema = z.output<typeof schema>;

const state = reactive({
  email: "",
});

const loading = ref<boolean>(false);
const loadingMessage = ref("");

const forgotPassword = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;
    loadingMessage.value = "Sending email...";

    $api
      .post("/password/forgot", {
        email: e.data.email,
      })
      .then((response) => {
        const toast = useToast();
        toast.add({
          title: "Password reset email sent",
          description: response.data.message,
          color: "success",
          icon: "tabler:check",
        });
      })
      .finally(() => {
        loading.value = false;
      });
  });
};
</script>

<template>
  <TCard
    class="w-screen-95 max-w-md select-none shadow-lg"
    :ui="{
      body: 'flex flex-col gap-5 sm:px-8 sm:py-12',
    }"
  >
    <div class="flex flex-col items-center gap-4">
      <div class="relative aspect-square h-20 w-20">
        <img
          src="/favicons/baguioseal.svg"
          class="absolute inset-0"
          :class="{ 'animate-ping': loading }"
        />
        <img src="/favicons/baguioseal.svg" class="absolute inset-0" />
      </div>
      <span class="text-2xl">{{ $config.public.product_name }}</span>
    </div>
    <TForm
      :schema="schema"
      :state="state"
      :validateOn="[]"
      class="flex flex-col gap-6"
      @submit="forgotPassword($event)"
    >
      <TFormField
        name="email"
        description="Enter your email and we'll send you a link to reset your password."
        required
        class="relative"
      >
        <TInput
          v-model="state.email"
          placeholder="example@email.com"
          color="neutral"
          size="md"
          :disabled="loading"
        />
      </TFormField>
      <TButton label="Send" block type="submit" :loading="loading" />

      <div class="flex items-center justify-center gap-2">
        <TButton
          variant="link"
          icon="tabler:chevron-left"
          :disabled="loading"
          :to="{ name: 'login' }"
        >
          Back to login
        </TButton>
      </div>
    </TForm>
  </TCard>
</template>
