<script setup lang="ts">
import Wrapper from "./wrapper.vue";

import type { LogSummaryItem } from "@/types/models/logs";

const { customize } = useIcon();
const { hexAToRGBA } = useColors();

const props = defineProps<{
  levelMap: Record<number, string>;
  colors: Record<number, string>;
  summary: Array<LogSummaryItem>;
}>();

const icons = ref<Record<number, string>>({
  1: "tabler:heartbeat",
  2: "tabler:shield-exclamation",
  3: "tabler:circle-x",
  4: "tabler:skull",
  5: "tabler:alert-triangle",
  6: "tabler:message-circle",
  7: "tabler:info-circle",
  8: "tabler:bug",
});

const totalSummary = computed(
  () => props.summary.find((s) => s.level == 0)?.total ?? 0,
);

const options = computed(() => ({
  colors: totalSummary.value > 0 ? Object.values(props.colors) : ["#afafaf"],
  labels: totalSummary.value > 0 ? Object.values(props.levelMap) : [""],
  chart: {
    type: "donut",
  },
  stroke: {
    colors: ["transparent"],
    lineCap: "",
  },
  legend: {
    show: false,
  },
  tooltip: {
    enabled: totalSummary.value > 0,
  },
  states: {
    hover: {
      filter: {
        type: totalSummary.value > 0 ? "lighten" : "none",
      },
    },
    active: {
      filter: {
        type: totalSummary.value > 0 ? "darken" : "none",
      },
    },
  },
  dataLabels: {
    enabled: totalSummary.value > 0,
  },
  plotOptions: {
    pie: {
      expandOnClick: false,
      donut: {
        labels: {
          show: false,
        },
        size: "60%",
      },
    },
  },
}));

const series = computed(() =>
  totalSummary.value > 0
    ? props.summary
        .filter((s) => Object.keys(props.levelMap).includes(s.level.toString()))
        .map((s) => s.total)
    : [1],
);
</script>

<template>
  <Wrapper>
    <template #header>
      <TIcon
        name="tabler:chart-donut"
        :customize="(c: string) => customize(c, { strokeWidth: 2 })"
        class="h-5 w-5"
      />
      <TTypography variant="h6">Overview</TTypography>
    </template>
    <div>
      <apexchart type="donut" :options :series></apexchart>
    </div>
    <div class="grid grid-cols-2 gap-2">
      <template v-for="(level, index) in levelMap" :key="level">
        <div
          class="flex items-center gap-1.5 rounded border py-1 pr-3 pl-2 capitalize"
          :style="{
            borderColor: colors[index],
            color: colors[index],
            backgroundColor: `rgba(${hexAToRGBA(colors[index]!).r},${hexAToRGBA(colors[index]!).g},${hexAToRGBA(colors[index]!).b},0.15)`,
          }"
        >
          <div class="flex flex-auto flex-col">
            <TTypography class="text-sm">{{ level }}</TTypography>
            <TTypography class="text-xl font-semibold xl:text-2xl">
              {{ summary.find((s) => s.level == index)?.total }}
            </TTypography>
          </div>
          <TIcon :name="icons[index]!" class="size-12" />
        </div>
      </template>
    </div>
  </Wrapper>
</template>
