<script setup lang="ts">
import ProfileEditor from "~/components/userEditor/profile/index.vue";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const editor = ref();

const redirect = () => {
  if (auth.hasProfileName) {
    router.push((route.query.redirect as string) ?? { name: "home" });
  }
};

watch(
  () => auth.hasProfileName,
  (val) => {
    if (val) {
      redirect();
    }
  },
  {
    immediate: true,
  },
);

onBeforeMount(() => redirect());
</script>

<template>
  <TCard
    :ui="{
      root: 'divide-y divide-neutral-200 dark:divide-neutral-600',
      header: 'px-3 py-2',
      body: 'flex flex-col gap-5 px-3 py-2',
    }"
  >
    <template #header>
      <TTypography> Your profile information needs to be updated. </TTypography>
    </template>
    <ProfileEditor
      ref="editor"
      @update:user="auth.setUser($event!)"
      api="/auth/profile-update"
    />
    <div class="flex items-center gap-2">
      <TButton
        label="Save"
        type="submit"
        @click="editor?.save()"
        :ui="{
          base: 'flex-auto justify-center',
        }"
      />
      <TButton
        label="Logout"
        icon="tabler:logout"
        variant="ghost"
        @click="auth.logout()"
      />
    </div>
  </TCard>
</template>
