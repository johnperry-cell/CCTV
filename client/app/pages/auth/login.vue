<script setup lang="ts">
import { z } from "zod";
import type { FormSubmitEvent } from "#ui/types";

const { $router } = useNuxtApp();
const $route = useRoute();
const auth = useAuthStore();
const { passwordRules, strength } = usePassword();

const passwordError = "Invalid password format";
const schema = z.object({
  email: z
    .string({
      message: "Username/Email is required",
    })
    .min(1, { message: "Username/Email is required" }),
  password: passwordRules({
    min: "Password is Required",
    max: passwordError,
    numbers: passwordError,
    symbols: passwordError,
    letters: passwordError,
    mixedCase: passwordError,
  }),
});

type Schema = z.output<typeof schema>;

const state = reactive({
  email: "",
  password: "",
  remember: false,
});
const showPassword = ref(false);
const loading = ref(false);
const loadingMessage = ref("");

const login = async (e: FormSubmitEvent<Schema>) => {
  loading.value = true;
  showPassword.value = false;
  loadingMessage.value = "Logging in...";
  auth
    .login(e.data)
    .then((res) => {
      $router.push(($route.query.redirect as string) || { name: "home" });
    })
    .catch((error) => {})
    .finally(() => {
      loading.value = false;
    });
};
</script>
<template>
  <div
    class="w-screen-95 bg-default/5 ring-default backdrop-blur-xs relative max-w-sm rounded-lg px-4 pb-4 pt-4 ring md:pb-8 md:pt-16"
  >
    <div
      class="md:ring-default md:bg-default inset-x-4 top-0 flex max-w-sm flex-col items-center gap-4 rounded-lg px-4 py-2 transition-all sm:inset-x-8 md:absolute md:-translate-y-1/2 md:ring"
    >
      <div v-if="false" class="relative aspect-square h-20 w-20">
        <img
          src="/favicons/baguioseal.svg"
          class="absolute inset-0"
          :class="{ 'animate-ping': loading }"
        />
        <img src="/favicons/baguioseal.svg" class="absolute inset-0" />
      </div>
      <div class="text-bol text-2xl">Login</div>
      <span class="text-muted text-sm">{{ $config.public.product_name }}</span>
    </div>
    <TForm
      :schema="schema"
      :state="state"
      :validateOn="[]"
      class="flex flex-col gap-5"
      @submit="login"
    >
      <TFormField label="Username / Email" name="email" required>
        <TInput
          v-model="state.email"
          placeholder="example@email.com"
          color="neutral"
          size="xl"
          :disabled="loading"
          class="w-full"
        />
      </TFormField>

      <TFormField label="Password" name="password" required>
        <template #hint>
          <div
            v-if="
              strength(state.password).score > 0 || state.password.length > 0
            "
            class="flex items-center gap-1 text-xs"
          >
            <TIcon
              name="tabler:circle-filled"
              :style="{
                color: strength(state.password).color,
              }"
            />
            {{ strength(state.password).complexity }}
          </div>
        </template>
        <TInput
          v-model="state.password"
          color="neutral"
          size="xl"
          :disabled="loading"
          :type="showPassword ? 'text' : 'password'"
          class="w-full"
        >
          <template #trailing>
            <TButton
              square
              icon="tabler:eye"
              variant="link"
              color="primary"
              :disabled="loading"
              :class="{
                'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200':
                  !showPassword,
              }"
              @click="showPassword = !showPassword"
            />
          </template>
        </TInput>
      </TFormField>

      <div class="flex items-center justify-between gap-2">
        <TCheckbox
          label="Remember me"
          name="remember"
          :disabled="loading"
          v-model="state.remember"
        />
        <TButton
          variant="link"
          :disabled="loading"
          :to="{ name: 'forgot-password' }"
        >
          Forgot Password?
        </TButton>
      </div>
      <div
        class="inset-x-4 transition-all sm:inset-x-8 md:absolute md:bottom-0 md:translate-y-1/2"
      >
        <TButton
          label="Sign in"
          block
          size="xl"
          type="submit"
          :loading="loading"
        />
      </div>
    </TForm>
  </div>
</template>
