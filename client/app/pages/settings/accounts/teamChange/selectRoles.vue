<script setup lang="ts">
import type { RoleItem, Team } from "~/types/models";

const { $api } = useNuxtApp();

const props = defineProps<{
  team: Team;
}>();

const emit = defineEmits(["back"]);

const selected = defineModel<Array<RoleItem>>({
  required: true,
});
const loading = defineModel<boolean>("loading");
const roles = ref<Array<RoleItem>>([]);
const search = ref<string>("");

const filtered = computed(() => {
  return roles.value.filter((t) =>
    t.name.toLowerCase().includes(search.value.toLowerCase()),
  );
});

const getRoles = () => {
  loading.value = true;
  $api
    .get("/user/roles", {
      params: {
        team: props.team.id,
      },
    })
    .then((response) => {
      roles.value = response.data.data as Array<RoleItem>;
    })
    .finally(() => {
      loading.value = false;
    });
};

const selectRole = (t: RoleItem) => {
  if (isSelected(t)) {
    selected.value = selected.value.filter((r) => r.id != t.id);
  } else {
    selected.value.push(t);
  }
};

const isSelected = (t: RoleItem) => {
  return !!selected.value?.find((r) => r.id == t.id);
};

onMounted(() => {
  getRoles();
});
</script>

<template>
  <div class="flex h-full flex-col gap-2 p-2">
    <div class="flex items-center gap-2">
      <TButton
        leading-icon="tabler:arrow-left"
        color="neutral"
        variant="ghost"
        :padded="false"
        class="self-stretch px-3"
        @click="emit('back')"
      />
      <TInput
        v-model="search"
        placeholder="search"
        :ui="{ root: 'flex-auto' }"
      />
    </div>
    <div class="flex flex-auto flex-col gap-2 overflow-auto">
      <template v-for="role in filtered" :key="role.id">
        <TButton
          size="lg"
          :variant="isSelected(role) ? 'solid' : 'ghost'"
          :color="isSelected(role) ? 'primary' : 'neutral'"
          @click="selectRole(role)"
        >
          <div class="flex flex-auto items-center gap-2">
            <TIcon
              :name="isSelected(role) ? 'tabler:checkbox' : 'tabler:square'"
              class="h-4 w-4"
            />
            <div class="flex items-center gap-2">
              {{ role.name }}
            </div>
          </div>
        </TButton>
      </template>
    </div>
  </div>
</template>
