<script setup lang="ts">
import type { LogSummary } from "~/types/models";
import Badger from "~/pages/settings/roles/roleBadge.vue";
import type { TableColumn } from "@nuxt/ui";

const props = defineProps<{
  data?: LogSummary;
  levels: Record<number, string>;
  monthly?: boolean;
  colors: Record<number, string>;
  loading?: boolean;
}>();

const { $dayjs } = useNuxtApp();

const columns = computed(() => {
  let cols = [
    {
      accessorKey: "date",
      header: "Date",
    },
  ];

  Object.keys(props.levels).forEach((l: string) => {
    cols.push({
      accessorKey: `${parseInt(l)}`,
      header: props.levels[parseInt(l)]!,
    });
  });

  cols.push({
    accessorKey: "total",
    header: "Total",
  });

  return cols as Array<TableColumn<LogSummary>>;
});

const rows = computed(() => {
  let r: any[] = [];
  Object.keys(props.data ?? {}).forEach((d: string) => {
    if (props.data) {
      r.push(
        Object.assign({}, props.data[d], {
          date: $dayjs(d).format(props.monthly ? "DD MMM YYYY" : "MMMM"),
        }),
      );
    }
  });
  return r;
});
</script>

<template>
  <TTable
    :columns
    :data="rows"
    :loading
    :ui="{
      th: 'capitalize',
    }"
  >
    <template
      v-for="index in Object.keys(levels)"
      :key="levels[index]"
      v-slot:[`${index}-cell`]="{ row }"
    >
      <div class="flex w-full items-center justify-center text-center">
        <Badger
          variant="outline"
          :color="
            row.original[`${index}`] > 0 ? colors[parseInt(index)]! : '#666'
          "
        >
          {{ row.original[`${index}`] }}
        </Badger>
      </div>
    </template>
  </TTable>
</template>
