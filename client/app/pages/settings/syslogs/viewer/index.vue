<script setup lang="ts">
import Wrapper from "../wrapper.vue";
import Summary from "./summary.vue";
import Daily from "./daily.vue";
import System from "./system/index.vue";

import type { LogSummary, LogData, LogSummaryItem } from "@/types/models";

const { customize } = useIcon();
const { $api } = useNuxtApp();
const loading = ref(false);
const props = defineProps<{
  levelMap: Record<number, string>;
  levels: Array<number>;
  modules: Array<string>;
  year: number;
  month?: number;
  day?: number;
  colors: Record<number, string>;
}>();

const emit = defineEmits(["update:day", "summarized"]);

const logs = ref<{
  yearly?: LogSummary;
  monthly?: LogSummary;
  daily?: Array<LogData>;
}>({
  yearly: undefined,
  monthly: undefined,
  daily: undefined,
});

const summarized = computed(() => {
  return logs.value.daily === undefined;
});

const pagination = ref({
  limit: 25,
  page: 1,
  total: 0,
});

const showSystemLogs = ref<boolean>(false);

const loadLogs = () => {
  loading.value = true;
  const paths = ["logsy", props.year, props.month, props.day];
  const end = paths.findIndex((p) => p === undefined);
  const path: string = paths.slice(0, end > -1 ? end : paths.length).join("/");

  $api
    .get(path, {
      params: {
        levels: props.levels ?? [],
        modules: props.modules ?? [],
        page: pagination.value.page,
        limit: pagination.value.limit,
      },
    })
    .then((response) => {
      if (props.day) {
        pagination.value.total = response.data.count;
      }
      if (props.month) {
        logs.value[props.day ? "daily" : "monthly"] = response.data.data;
      } else {
        logs.value.yearly = response.data.data;
      }

      emit("summarized", response.data.summary as Array<LogSummaryItem>);
    })
    .finally(() => {
      loading.value = false;
    });
};

watch(
  [
    () => props.year,
    () => props.month,
    () => props.day,
    () => props.levels,
    () => props.modules,
    pagination,
  ],
  (newVal, oldVal) => {
    if (newVal[0] !== oldVal[0]) {
      logs.value.daily = logs.value.monthly = undefined;
    }

    if (newVal[1] !== oldVal[1]) {
      logs.value.daily = undefined;
    }

    loadLogs();
  },
  { deep: true },
);

onMounted(() => {
  loadLogs();
});
</script>

<template>
  <Wrapper>
    <template #header>
      <TIcon
        name="tabler:terminal-2"
        :customize="(c: string) => customize(c, { strokeWidth: 2 })"
        class="h-5 w-5"
      />
      <TTypography variant="h6">Logs</TTypography>
      <div class="flex flex-auto items-center justify-end gap-2">
        <TButton
          label="View System Logs"
          color="neutral"
          @click="showSystemLogs = true"
        />
      </div>
    </template>
    <Summary
      v-if="summarized"
      :levels="levelMap"
      :colors
      :loading
      :data="(logs.monthly ?? logs.yearly)!"
      :monthly="!!logs.monthly"
      class="h-full max-h-full"
    />
    <Daily
      v-else-if="logs.daily"
      v-model:pagination="pagination"
      :levelMap
      :colors
      :loading
      :data="logs.daily"
      class="h-full max-h-full"
    />

    <TModal
      v-model:open="showSystemLogs"
      title="System Logs"
      description="View system logs"
      :ui="{
        content: 'w-screen-90 sm:max-w-7xl',
      }"
    >
      <template #content="{ close }">
        <System @close="close" />
      </template>
    </TModal>
  </Wrapper>
</template>
