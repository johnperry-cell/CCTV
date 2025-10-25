<script setup lang="ts">
import Filters from "./filters/index2.vue";
import Viewer from "./viewer/index.vue";
import Overview from "./overview.vue";

import type { LogSummaryItem } from "~/types/models";

const { $api } = useNuxtApp();

const levelMap = ref<Record<number, string>>({});
const moduleOptions = ref<Array<string>>([]);
const loading = ref<boolean>(true);

const colors = ref<Record<number, string>>({
  1: "#EF4444",
  2: "#F66D44",
  3: "#BC2324",
  4: "#DC2626",
  5: "#F89500",
  6: "#3B82F6",
  7: "#16A34A",
  8: "#8695AA",
});

const data = ref<{
  year: number;
  month: number;
  day: number;
  levels: Array<number>;
  modules: Array<string>;
}>({
  year: new Date().getFullYear(),
  month: new Date().getMonth() + 1,
  day: new Date().getDate(),
  levels: [] as Array<number>,
  modules: [] as Array<string>,
});

const logSummary = ref<Array<LogSummaryItem>>([]);

const loadLevels = () => {
  loading.value = true;
  $api
    .get(`log/setup`)
    .then((response) => {
      data.value.levels = Object.keys(response.data.levels).map((l) =>
        parseInt(l),
      );
      data.value.modules = response.data.modules;
      levelMap.value = response.data.levels;
      moduleOptions.value = response.data.modules;
    })
    .finally(() => {
      loading.value = false;
    });
};

const onSummarized = (data: Array<LogSummaryItem>) => {
  logSummary.value = data;
};

onMounted(() => {
  loadLevels();
});
</script>

<template>
  <TTypography v-if="loading" class="px-3 py-6 text-center text-neutral-400/50">
    Preparing viewer...
  </TTypography>
  <div
    v-else
    class="relative flex h-full max-h-full items-start divide-x divide-neutral-100 overflow-x-auto dark:divide-neutral-700"
  >
    <div class="h-full w-72">
      <Filters
        v-model:year="data.year"
        v-model:month="data.month"
        v-model:day="data.day"
        v-model:levels="data.levels"
        v-model:modules="data.modules"
        :moduleOptions
        :levelMap
        :colors="colors"
        class="h-full"
      />
    </div>
    <div class="relative !min-w-[32rem] flex-auto self-stretch">
      <Viewer
        v-bind="data"
        :levelMap
        :colors
        class="absolute inset-0"
        @summarized="onSummarized"
      />
    </div>
    <div class="h-full min-w-96">
      <Overview :summary="logSummary" :levelMap :colors class="h-full" />
    </div>
  </div>
</template>
