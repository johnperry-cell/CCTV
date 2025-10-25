<script setup lang="ts">
import type { Team } from "~/types/models/team";

const { $api } = useNuxtApp();

const team = defineModel<Team>();
const loading = defineModel<boolean>("loading");

const teams = ref<Array<Team>>([]);
const search = ref<string>("");

const searchTeams = async (search: string = "") => {
  loading.value = true;
  const response = await $api.get("/auth/teams", { params: { search } });
  loading.value = false;
  teams.value = response.data.data;
  return response.data.data;
};

const filtered = computed(() => {
  return teams.value.filter((t) =>
    t.name.toLowerCase().includes(search.value.toLowerCase()),
  );
});

onMounted(() => {
  searchTeams();
});
</script>

<template>
  <div class="flex h-full flex-col gap-2 p-2">
    <TInput v-model="search" placeholder="search" />
    <div class="flex flex-auto flex-col gap-1 overflow-auto">
      <template v-for="t in filtered" :key="t.id">
        <TButton
          size="lg"
          :variant="team?.id == t.id ? 'solid' : 'ghost'"
          :color="team?.id == t.id ? 'primary' : 'neutral'"
          class="text-start"
          @click="team = t"
        >
          <div class="flex flex-auto items-center gap-2">
            <TIcon
              :name="
                team?.id == t.id ? 'tabler:circle-filled' : 'tabler:circle'
              "
              class="h-4 w-4"
            />
            <div class="flex items-center gap-2">
              {{ t.name }}
            </div>
          </div>
        </TButton>
      </template>
    </div>
  </div>
</template>
