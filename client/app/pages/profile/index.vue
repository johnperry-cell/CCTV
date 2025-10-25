<script setup lang="ts">
import type {
  ProfileImage,
  User,
  Address as AddressType,
} from "~/types/models/users";
import UserItem from "./userItem.vue";
import Avatar from "~/components/userEditor/avatar/index.vue";
import ProfileTab from "./tabs/profile.vue";
import SecurityTab from "./tabs/security.vue";
import AddressTab from "./tabs/address.vue";

const auth = useAuthStore();
const { $api } = useNuxtApp();

const user = ref<User>();
const loading = ref(false);
const modal = ref<{
  show: boolean;
}>({
  show: false,
});
const openModal = () => {
  modal.value.show = true;
};
const items = shallowRef([
  {
    key: "profile",
    label: "Profile",
    icon: "tabler:user",
    description:
      "Make changes to your account here. Click save when you're done.",
    component: ProfileTab,
  },
  {
    key: "security",
    label: "Security",
    icon: "tabler:lock",
    description:
      "Change your security details here. Note! Changing your username or password will log you out.",
    component: SecurityTab,
  },
  {
    key: "address",
    label: "Address",
    icon: "tabler:map",
    description:
      "Make changes to your address here. You can add multiple addresses.",
    component: AddressTab,
  },
]);

const loadProfile = () => {
  loading.value = true;
  $api
    .get("/auth/profile")
    .then((response) => {
      user.value = response.data.data as User;
    })
    .finally(() => {
      loading.value = false;
    });
};

watch(user, (val) => {
  if (val) {
    auth.setUser(val);
    // if (auth.profile) {
    //   const img = val.profile?.images?.[0] ?? null;
    //   const addr = val.profile?.addresses?.[0] ?? null;
    //   auth.profile.images = img ? [img as ProfileImage] : [];
    //   auth.profile.addresses = addr ? [addr as AddressType] : [];
    // }
  }
});

watch([() => auth.email, () => auth.verified], () => {
  if (user.value) {
    user.value.email = auth.email;
    user.value.verified = auth.verified;
  }
});

onMounted(loadProfile);
</script>

<template>
  <div v-if="user" class="px-4 py-4 lg:px-56">
    <div class="grid gap-4 md:grid-cols-6">
      <div class="md:col-span-2">
        <UserItem :user @addAvatar="openModal" />
      </div>
      <div class="md:col-span-4">
        <TTabs
          :items="items"
          class="w-full"
          :ui="{
            list: 'border border-neutral-200 dark:border-neutral-700',
          }"
        >
          <template #content="{ item }">
            <component
              :is="item.component"
              :label="item.label"
              :icon="item.icon"
              :description="item.description"
              v-model:user="user"
              :ui="{
                body: 'flex flex-col gap-5',
                footer: 'flex items-center justify-end',
              }"
            />
          </template>
        </TTabs>
      </div>
    </div>

    <TModal
      v-model:open="modal.show"
      :dismissible="false"
      title="Upload Photo"
      description="Upload a profile photo for your account."
      @close="modal.show = false"
    >
      <template #body>
        <Avatar
          v-model:user="user"
          :api="{
            create: `auth/avatars`,
            update: `auth/avatars`,
            delete: `auth/avatars`,
          }"
          class="rounded"
        />
      </template>
    </TModal>
  </div>
  <div v-else class="py-4 lg:px-56">Loading...</div>
</template>
