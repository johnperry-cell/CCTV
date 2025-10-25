<script setup lang="ts">
import type { User } from "~/types/models/users";
import ProfileEditor from "~/components/userEditor/profile/index.vue";
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

const editor = ref();
const canSave = ref(false);
</script>

<template>
  <Wrapper :label :description>
    <ProfileEditor
      api="/auth/profile"
      ref="editor"
      v-model:user="user"
      :disabled="!$guard.can('self_update-profile')"
      @modifying="canSave = $event"
    />
    <template #footer>
      <TButtonGroup v-if="$guard.can('self_update-profile')">
        <TButton label="Save" :disabled="!canSave" @click="editor?.save()" />
        <TButton
          icon="tabler:refresh"
          :disabled="!canSave"
          variant="outline"
          @click="editor?.reset()"
        />
      </TButtonGroup>
      <Warning v-else>
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
