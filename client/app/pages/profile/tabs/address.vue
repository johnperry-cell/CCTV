<script setup lang="ts">
import type { User } from "~/types/models/users";
import AddressEditor from "~/components/userEditor/address/index.vue";
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
</script>

<template>
  <Wrapper :label :description class="min-h-96">
    <AddressEditor
      v-model:user="user"
      :canEdit="$guard.can('self_update-profile')"
      :api="{
        editor: '/auth/address',
        delete: '/auth/address',
        primary: '/auth/address-primary',
      }"
    />
    <template v-if="!$guard.can('self_update-profile')" #footer>
      <Warning>
        <ul class="list-disc space-y-2 pl-4">
          <li>
            <b>Edit Profile Information: </b>You cannot change details like
            name, address, or avatar.
          </li>
        </ul>
      </Warning>
    </template>
  </Wrapper>
</template>
