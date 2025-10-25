<script setup lang="ts">
import type { User } from "~/types/models/users";
import type { Address } from "~/types/models/users";

import Overlay from "../utils/overlay.vue";
import Editor from "./editor.vue";
import AddressItem from "./item.vue";
import Delete from "./delete.vue";

const props = defineProps<{
  canEdit: boolean;
  api: {
    editor: string;
    delete: string;
    primary: string;
  };
}>();

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const modal = ref<{
  show: boolean;
  data?: Address;
  type: string;
}>({
  show: false,
  data: undefined,
  type: "editor",
});

const openModal = (data?: Address, type: string = "") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};
</script>

<template>
  <div class="flex flex-col gap-2 overflow-hidden">
    <TTypography
      variant="h5"
      class="sticky -top-2 flex items-center justify-between gap-2 py-2 text-base font-semibold"
    >
      <span> Address Information </span>
      <div v-if="canEdit" class="flex h-7 items-center gap-2">
        <TButton
          label="Add Address"
          size="xs"
          icon="tabler:plus"
          @click="openModal(undefined, 'editor')"
        />
      </div>
    </TTypography>
    <div class="max-h-96 flex-auto space-y-4 overflow-y-auto pb-5">
      <template
        v-for="address in user?.profile?.addresses ?? []"
        :key="address.id"
      >
        <AddressItem
          v-model:user="user"
          :address
          :api="api.primary"
          :canEdit="canEdit"
          class="py-3"
          @edit="openModal(address, 'editor')"
          @delete="openModal(address, 'delete')"
        />
      </template>
      <template v-if="(user?.profile?.addresses ?? []).length <= 0">
        <TTypography
          variant="sm"
          class="flex flex-col items-center text-center text-gray-400"
        >
          <TIcon name="tabler:address-book-off" class="h-8 w-8" />
          No address found
        </TTypography>
      </template>
    </div>

    <Overlay
      v-if="canEdit"
      v-model="modal.show"
      prevent-close
      v-slot="{ close }"
    >
      <Editor
        v-if="modal.type === 'editor'"
        v-model="user"
        :address="modal.data"
        :api="api.editor"
        @update:modelValue="close()"
        @close="close"
      />
      <Delete
        v-else-if="modal.type === 'delete'"
        v-model:user="user"
        :address="modal.data!"
        :api="api.delete"
        @update:user="close()"
        @close="close()"
      />
    </Overlay>
  </div>
</template>
