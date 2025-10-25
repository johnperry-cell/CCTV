<script setup lang="ts">
import EmailEditor from "~/components/userEditor/account/email.vue";
import Content from "./content.vue";
import type { User } from "~/types/models/users";

const $auth = useAuthStore();
const $guard = useGuard();
const { $router, $api } = useNuxtApp();
const $route = useRoute();

const loading = ref(false);
const loggingOut = ref(false);

const title = computed(() =>
  $auth.email ? "Verify your email address" : "Unverified Account",
);
const message = computed(() =>
  $auth.email
    ? "Check your email to continue with the verification process."
    : `Verification of your account is required before you can continue.  ${
        $guard.can("self_update-account")
          ? "Please set up an email address to proceed with the verification process."
          : "Please contact your account administrator to verify your account."
      }`,
);

const logout = () => {
  loggingOut.value = true;
  $auth.logout().finally(() => {
    $router.push(($route.query.redirect as string) || { name: "login" });
    loggingOut.value = false;
  });
};

const resendVerification = () => {
  loading.value = true;
  $api.post("/auth/email/resend").finally(() => {
    loading.value = false;
  });
};

watch(
  () => $auth.verified,
  (val) => {
    if (val) {
      $router.push(($route.query.redirect as string) || { name: "home" });
    }
  },
  { immediate: true },
);
</script>

<template>
  <Content icon="tabler:alert-triangle" :title :message>
    <TTypography variant="md" class="max-w-lg text-center">
      {{ message }}
    </TTypography>
    <template v-if="$auth.email">
      <TTypography variant="sm" class="text-gray-400"> - OR - </TTypography>
      <TButton
        block
        :label="loading ? '' : 'Resend verification email'"
        size="lg"
        :loading
        @click="resendVerification"
      />
    </template>

    <template v-else-if="$guard.can('self_update-account')">
      <TCard
        :ui="{
          header: 'px-3 py-1',
          body: 'flex flex-col gap-5 px-3 py-2',
        }"
      >
        <template #header>
          <TTypography class="font-semibold leading-none">
            Set up an email address
          </TTypography>
        </template>
        <EmailEditor
          api="/auth/email"
          canEdit
          @update:user="$auth.setUser($event! as User)"
        />
      </TCard>
    </template>

    <template #actions>
      <div class="flex w-full items-center justify-between">
        <div class="flex flex-auto items-center gap-2">
          <TButton label="Go Back" @click="$router.back()" />
        </div>
        <TButton
          label="logout"
          :padded="false"
          variant="link"
          :loading="loggingOut"
          @click="logout"
        />
      </div>
    </template>
  </Content>
</template>
