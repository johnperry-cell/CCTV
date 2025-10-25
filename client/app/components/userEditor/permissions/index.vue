<script setup lang="ts">
import { z } from "zod";
import type { Form, FormSubmitEvent } from "#ui/types";
import type { PermissionItem } from "~/types/models/permission";
import type { RoleItem } from "~/types/models/roles";
import type { User, UserRole } from "~/types/models/users";

import PermissionSelect from "./permissionsSelect.vue";

const props = defineProps({
  canGiveDirectPermissions: { type: Boolean, default: false },
});
const emit = defineEmits(["modifying"]);

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);
const roles = ref<Array<RoleItem>>([]);

const schema = z.object({
  roles: z.array(z.string()).min(1, { message: "Roles is required" }),
  permissions: z
    .array(
      z.object({
        id: z.string(),
      }),
    )
    .min(0),
});
type Schema = z.output<typeof schema>;
const state = ref<{
  roles: Array<string>;
  permissions: Array<PermissionItem>;
}>({
  roles: [],
  permissions: [],
});
const form = ref<Form<Schema>>();

const rolePerms = computed<Array<string>>(() => {
  const rs = roles.value.filter((r) => state.value.roles.includes(r.id));
  let p: Array<string> = [];

  rs.forEach((r) => {
    const ps = r.permissions.map((p) => p.id);
    ps.forEach((pp) => {
      if (!p.includes(pp)) p.push(pp);
    });
  });

  return p;
});

const getRoles = () => {
  loading.value = true;
  $api
    .get("/user/roles?perms=true")
    .then((response) => {
      roles.value = response.data.data as Array<RoleItem>;
    })
    .finally(() => {
      loading.value = false;
    });
};

const saveProfile = (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;
    $api
      .patch(
        `/user/permissions/${user.value?.id}`,
        Object.assign({}, e.data, {
          permissions: e.data.permissions.map((p) => p.id),
        }),
      )
      .then((response) => {
        user.value = response.data.data as User;
        toast.add({
          title: "Profile Updated",
          description: response.data.message ?? "User Roles saved successfully",
          color: "primary",
          icon: "tabler:circle-check",
        });
        emit("modifying", false);
        resolve(response);
      })
      .catch((error) => {
        const errors = Object.keys(error.response.data.errors).map((k) => {
          return { path: k, message: error.response.data.errors[k][0] };
        });
        form.value?.setErrors(errors);
        reject(error);
      })
      .finally(() => {
        loading.value = false;
      });
  });
};

const reset = () => {
  state.value.roles = user.value?.roles.map((r) => (r as UserRole).id) ?? [];
  state.value.permissions =
    user.value?.permissions.map(
      (p) =>
        ({
          id: (p as PermissionItem).id,
          name: (p as PermissionItem).name,
        }) as PermissionItem,
    ) ?? [];
  form.value?.clear();
};

watch(
  state,
  (val) => {
    const roleChanged = !(
      user.value?.roles.length == val.roles.length &&
      user.value?.roles.every((r) => val.roles.includes((r as UserRole).id))
    );
    const perms = val.permissions.map((p) => p.id);
    const permsChanged = !(
      user.value?.permissions.length == val.permissions.length &&
      user.value?.permissions.every((p) =>
        perms.includes((p as PermissionItem).id),
      )
    );

    emit("modifying", roleChanged || permsChanged);
  },
  { deep: true },
);

onMounted(() => {
  getRoles();
  reset();
});
defineExpose({
  reset,
  save: () => form.value?.submit(),
});
</script>

<template>
  <TForm
    ref="form"
    :state
    :schema
    :validateOn="[]"
    class="flex max-h-full flex-auto flex-col gap-4"
    @submit="saveProfile($event)"
  >
    <TFormField label="Roles" name="roles" required>
      <TSelectMenu
        v-model="state.roles"
        :items="roles"
        :loading
        multiple
        size="md"
        color="neutral"
        placeholer="Select Role"
        valueKey="id"
        labelKey="name"
        :ui="{
          base: 'w-full',
        }"
      />
    </TFormField>
    <PermissionSelect
      searchApi="/user/permissions"
      v-model="state.permissions"
      v-model:loading="loading"
      :locked="rolePerms"
      viewSelected
      :viewOnly="!canGiveDirectPermissions"
      :compact="false"
      striped
      class="flex-auto gap-2"
    />
  </TForm>
</template>
