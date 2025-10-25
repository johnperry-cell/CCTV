<script setup lang="ts">
import type { User } from "~/types/models/users";
import Email from "./email.vue";
import Username from "./username.vue";
import Password from "./password.vue";

const props = defineProps<{
  api: {
    email: string;
    username: string;
    password: string;
  };
  canUpdateEmail: boolean;
  canUpdateUsername: boolean;
  canUpdatePassword: boolean;
}>();

const emit = defineEmits([
  "passwordChanged",
  "usernameChanged",
  "emailChanged",
]);

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});
</script>

<template>
  <div class="flex flex-col gap-5">
    <Email
      :api="api.email"
      v-model:user="user"
      :canEdit="canUpdateEmail"
      @update:user="emit('emailChanged', $event)"
    />
    <!-- <TDivider label="" :ui="{ border: { base: 'dark:border-gray-400/25' } }" /> -->
    <Username
      :api="api.username"
      v-model:user="user"
      :canEdit="canUpdateUsername"
      @update:user="emit('usernameChanged', $event)"
    />
    <!-- <TDivider label="" :ui="{ border: { base: 'dark:border-gray-400/25' } }" /> -->
    <Password
      v-if="canUpdatePassword"
      :api="api.password"
      v-model:user="user"
      @update:user="emit('passwordChanged', $event)"
    />
  </div>
</template>
