<script setup lang="ts">
import type { User, ProfileImage, Address } from "~/types/models/users";

import RoleBadge from "~/pages/settings/roles/roleBadge.vue";
import UserAvatar from "./avatar.vue";

const props = defineProps<{ user: Omit<User, "id"> }>();

const emit = defineEmits(["addAvatar"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);

const image = computed(() =>
  props.user.profile?.images?.find((i: ProfileImage) => i.primary),
);

const address = computed(() =>
  props.user.profile?.addresses?.find((a: Address) => a.isMain),
);

const sendVerificationLink = () => {
  loading.value = true;
  $api
    .post("/auth/email/resend")
    .then(() => {
      toast.add({
        title: "Verification email sent",
        color: "success",
        icon: "tabler:check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard class="p-5">
    <div class="mb-4 flex flex-col items-center gap-4">
      <UserAvatar
        :alt="image?.alt ?? 'user_avatar'"
        :src="image?.url?.lg"
        icon="tabler:user-filled"
        @click="emit('addAvatar')"
      />
      <div class="text-center">
        <TTypography variant="h5" class="font-semibold">
          {{ user.profile?.full_name }}
        </TTypography>
        <TTypography class="text-gray-400">
          {{ user.username }}
        </TTypography>
      </div>
      <div class="flex flex-wrap gap-2">
        <template
          v-for="role in user.roles"
          :key="`${role.name}_${role.color}`"
        >
          <RoleBadge
            :label="role.name"
            :color="role.color"
            size="sm"
            variant="subtle"
            :ui="{ rounded: 'rounded-full' }"
          />
        </template>
      </div>
    </div>
    <div>
      <TTypography variant="h6" class="mb-3 border-b pb-3 font-semibold">
        Account Details
      </TTypography>
      <ul class="space-y-2">
        <li>
          <div class="w-full">
            <div class="flex items-center justify-end">
              <TButton
                v-if="!!user.email && !user.verified"
                size="xs"
                variant="link"
                :label="loading ? '' : 'Resend Verification Link'"
                :loading
                @click="sendVerificationLink"
              />
            </div>
            <div class="flex flex-auto items-center gap-2">
              <span class="font-semibold">Email:</span>
              <span
                class="text-neutral-400"
                :class="{
                  'text-sm': !user.email,
                }"
              >
                {{ user.email ?? "(No Email)" }}
              </span>
            </div>
          </div>
        </li>
        <li class="space-x-2">
          <span class="font-semibold">Birthday:</span>
          <span
            class="text-neutral-400"
            :class="{ 'text-sm': !user.profile?.birthdate }"
          >
            {{
              user.profile?.birthdate
                ? $dayjs(user.profile?.birthdate).format("DD MMM YYYY")
                : "(Not set)"
            }}
          </span>
        </li>
        <li class="space-x-2">
          <span class="font-semibold">Gender:</span>
          <span
            class="text-neutral-400"
            :class="{ 'text-sm': !user.profile?.gender }"
          >
            {{ user.profile?.gender?.name ?? "(Not set)" }}
          </span>
        </li>
        <li class="space-x-2">
          <span class="font-semibold">Default Address:</span>
          <span
            class="text-neutral-400"
            :class="{
              'text-sm': !address,
            }"
          >
            {{ address?.full ?? "(Not Address)" }}
          </span>
        </li>
      </ul>
    </div>
  </TCard>
</template>
