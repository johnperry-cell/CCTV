<script setup lang="ts">
import type { User } from "~/types/models/users";
import AccountEditor from "~/components/userEditor/account/index.vue";
import Wrapper from "./wrapper.vue";
import Warning from "./warning.vue";
const props = defineProps<{
  label: string;
  description: string;
}>();

const user = defineModel("user", {
  type: Object as PropType<User>,
  required: true,
});

const $guard = useGuard();
const auth = useAuthStore();
</script>

<template>
  <Wrapper :label :description>
    <AccountEditor
      :api="{
        email: `/auth/email`,
        username: `/auth/username`,
        password: `/auth/password`,
      }"
      v-model:user="user"
      :canUpdateEmail="$guard.can('self_update-account')"
      :canUpdateUsername="$guard.can('self_update-account')"
      :canUpdatePassword="$guard.can('self_change-password')"
      @usernameChanged="auth.logout()"
    />

    <template
      v-if="!$guard.canAll('self_update-account', 'self_change-password')"
      #footer
    >
      <Warning>
        <ul class="list-disc space-y-2 pl-4">
          <template v-if="!$guard.can('self_update-account')">
            <li>
              <b>Update Email: </b>The registered email address is fixed and
              cannot be updated.
            </li>
            <li>
              <b>Update Username: </b>The registered username is fixed and
              cannot be updated.
            </li>
          </template>
          <template v-if="!$guard.can('self_change-password')">
            <li>
              <b>Change Password: </b>Password modifications are restricted.
            </li>
          </template>
        </ul>
      </Warning>
    </template>
  </Wrapper>
</template>
