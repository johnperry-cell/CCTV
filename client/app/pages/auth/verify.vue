<script setup lang="ts">
import type { User } from "~/types/models";

const auth = useAuthStore();
const { $api } = useNuxtApp();
const $route = useRoute();
const toast = useToast();

const loading = ref(false);
const error = ref<string>();

const verify = () => {
  loading.value = true;
  const id = $route.params.id;
  const hash = $route.params.hash;
  const expires = $route.query.expires;
  const signature = $route.query.signature;

  $api
    .get(`/auth/email/verify/${id}/${hash}`, {
      params: { expires, signature },
    })
    .then((response) => {
      auth.setUser(response.data.data as User);
      toast.add({
        title: "Email verified",
        description: response.data.message,
        color: "success",
        icon: "tabler:check",
      });
    })
    .catch((e) => {
      error.value = e.response.data?.message ?? "Failed to verify email";
    })
    .finally(() => {
      loading.value = false;
    });
};

// watch(
//   () => auth.verified,
//   () => {
//     if (auth.verified) {
//       $router.push(($route.query.redirect as string) || { name: "home" });
//     }
//   },
//   { immediate: true },
// );

onMounted(() => {
  verify();
});
</script>

<template>
  <TCard
    :ui="{
      root: 'w-screen-95 relative max-w-md divide-y divide-neutral-400/25 px-8',
      header: 'flex flex-col items-center gap-5 px-3 pb-5 pt-10',
      body: 'flex flex-col items-center gap-5 px-3 py-5 text-center',
    }"
  >
    <template #header>
      <div
        class="flex items-center justify-center rounded-full bg-neutral-50 p-2 ring-4 dark:bg-neutral-800"
        :class="{
          'ring-error-500': !!error,
          'ring-neutral-200 dark:ring-neutral-600': loading,
          'ring-success-400 dark:ring-success-600': !loading && !error,
        }"
      >
        <TIcon
          :name="
            !!error
              ? 'tabler:mail-exclamation'
              : loading
                ? 'tabler:loader-2'
                : 'tabler:mail-check'
          "
          class="h-16 w-16"
          :class="{
            'animate-spin': loading,
            'text-error-500': error,
            'text-success-400 dark:text-success-600': !loading && !error,
          }"
        />
      </div>
      <TTypography v-if="loading" class="font-semibold">
        Verifying your email
      </TTypography>
      <template v-else>
        <TTypography class="font-semibold">
          {{ error || "Email verified, you can safely close this window" }}
        </TTypography>
      </template>
    </template>
  </TCard>
</template>
