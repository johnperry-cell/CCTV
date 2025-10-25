<script setup lang="ts">
import type { Team } from "~/types/models/team";

const config = useAppConfig();
const { $api } = useNuxtApp();
const user = useAuthStore();

const loading = ref<boolean>(false);
const teams = ref<Array<Team>>([]);

const team = computed({
  get: () => user.team,
  set: (team: Team) => {
    loading.value = true;
    user.switchTeam(team).finally(() => {
      loading.value = false;
    });
  },
});

const searchTeams = async (search: string) => {
  if (user.canSwitchTeam) {
    loading.value = true;
    const response = await $api.get("/auth/teams", { params: { search } });
    loading.value = false;
    teams.value = response.data.data;
    return response.data.data;
  }
};

onMounted(searchTeams);
</script>

<template>
  <TSelectMenu
    v-if="user.canSwitchTeam"
    v-model="team"
    :items="teams"
    labelKey="name"
    class="w-screen-95 max-w-64"
  >
    <template #item="{ item }">
      <TTooltip
        :text="item.name"
        :content="{
          align: 'center',
          side: 'right',
        }"
      >
        <span class="truncate">
          {{ item.name }}
        </span>
      </TTooltip>
    </template>
  </TSelectMenu>
</template>
