<script setup lang="ts">
import Wrapper from "../wrapper.vue";

import FilterItem from "./filterItem.vue";
import LevelSelect from "./levelSelect.vue";
import ModuleSelect from "./moduleSelect.vue";

const { customize } = useIcon();
const dayjs = useDayjs();
const props = defineProps<{
  levelMap: Record<number, string>;
  moduleOptions: Array<string>;
  colors: Record<number, string>;
}>();
const year = defineModel<number>("year");
const month = defineModel<number>("month");
const day = defineModel<number>("day");
const levels = defineModel<number[]>("levels", {
  required: true,
});
const modules = defineModel<string[]>("modules", {
  required: true,
});

const date = computed({
  get: () => {
    return dayjs(`${year.value}-${month.value}-${day.value}`).format(
      "YYYY-MM-DD",
    );
  },
  set: (d: string) => {
    if (d) {
      year.value = d ? dayjs(d).year() : dayjs().year();
      month.value = d ? dayjs(d).month() + 1 : undefined;
      day.value = d ? dayjs(d).date() : undefined;
    }
  },
});
</script>

<style lang="scss">
.vc-dark {
  --vc-day-content-disabled-color: rgb(var(--color-gray-500)) !important;
}
.vc-highlight-content-solid {
  --vc-day-content-disabled-color: rgb(var(--color-gray-100)) !important;
}
.is-not-in-month * {
  opacity: 1 !important;
  pointer-events: inherit !important;
  color: var(--vc-day-content-disabled-color) !important;
}
</style>

<template>
  <Wrapper>
    <template #header>
      <TIcon
        name="tabler:adjustments"
        :customize="(c: string) => customize(c, { strokeWidth: 2 })"
        class="h-5 w-5"
      />
      <TTypography variant="h6">Filters</TTypography>
    </template>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
      <FilterItem initialOpen icon="tabler:calendar" label="Select Date">
        <TDatePicker v-model="date" :maxDate="dayjs().toDate()" />
      </FilterItem>
      <FilterItem icon="tabler:stack-middle" label="Level">
        <LevelSelect v-model="levels" :levelMap :colors :cols="2" />
      </FilterItem>
      <FilterItem icon="tabler:packages" label="Module">
        <ModuleSelect v-model="modules" :moduleOptions :cols="1" />
      </FilterItem>
    </div>
  </Wrapper>
</template>
