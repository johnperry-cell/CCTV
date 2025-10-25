<script setup lang="ts">
import type { User, Team, RoleItem } from "~/types/models";
import TeamSelect from "./teamSelect.vue";
import RoleSelect from "./selectRoles.vue";

const { $api } = useNuxtApp();
const auth = useAuthStore();
const $toast = useToast();

const emit = defineEmits(["close", "transfer"]);

const user = defineModel<User>({
  required: true,
});
const team = ref<Team>(auth.team!);
const roles = ref<Array<RoleItem>>([]);
const selectRoles = ref(false);

const loading = ref<boolean>(false);

const backToTeamSelect = () => {
  selectRoles.value = false;
  roles.value = [];
};

const transferUser = () => {
  loading.value = true;

  $api
    .put(`user/team/${user.value.id}`, {
      team: team.value.id,
      roles: roles.value.map((r) => r.id),
    })
    .then((response) => {
      emit("transfer", user.value);
      $toast.add({
        title: "Success",
        description: response.data.message ?? "User team changed successfully!",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard
    :ui="{
      root: 'h-screen-90 lg:h-screen-80',
      body: 'p-0 sm:p-0',
      footer: 'flex items-center justify-end gap-2',
    }"
  >
    <template #header>
      <div class="flex flex-auto items-center gap-2">
        <TIcon name="tabler:alert-triangle" class="text-warning-500 h-7 w-7" />
        <TTypography variant="md" class="font-semibold">
          Change Team
        </TTypography>
      </div>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        @click="emit('close')"
      />
    </template>
    <TeamSelect
      v-if="!selectRoles"
      v-model="team"
      @update:modelValue="selectRoles = true"
      v-model:loading="loading"
    />
    <RoleSelect
      v-else
      v-model="roles"
      v-model:loading="loading"
      :team
      @back="backToTeamSelect"
    />
    <template #footer>
      <TButton
        label="Transfer User"
        color="primary"
        :disabled="!team || roles.length <= 0"
        @click="transferUser"
      />
      <TButton
        label="Close"
        variant="ghost"
        color="neutral"
        @click="emit('close')"
      />
    </template>

    <TInnerLoading :active="loading" />
  </TCard>
</template>
