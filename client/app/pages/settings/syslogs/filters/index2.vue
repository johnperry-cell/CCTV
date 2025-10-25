<script setup lang="ts">
import Wrapper from "../wrapper.vue";

const { customize } = useIcon();
const dayjs = useDayjs();
// const props = defineProps<{}>();
const year = defineModel<number>("year");
const month = defineModel<number>("month");
const day = defineModel<number>("day");
const dayOpen = ref(false);

const years = computed(() =>
  Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - i),
);
const months = computed(() =>
  Array.from({ length: 12 }, (_, i) => {
    const m = dayjs().month(i);
    return {
      value: parseInt(m.format("M")),
      label: m.format("MMMM"),
    };
  }),
);

const days = computed(() => {
  let d = 0;

  if (year.value && month.value) {
    d = dayjs().year(year.value).month(month.value).daysInMonth();
  }

  return Array.from({ length: d }, (_, i) => i + 1);
});
</script>

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
    <TFormField label="Year">
      <TSelect
        v-model="year"
        :items="years"
        class="w-full"
        @update:modelValue="((month = undefined), (day = undefined))"
      />
    </TFormField>
    <TFormField label="Month">
      <TSelect
        v-model="month"
        :items="months"
        valueKey="value"
        labelKey="label"
        class="w-full"
        @update:modelValue="day = undefined"
      />
    </TFormField>
    <TFormField v-if="false" label="Day">
      <TSelect v-model="day" :options="days" />
    </TFormField>

    <TPopover v-model:open="dayOpen">
      <TFormField label="Day" class="w-full">
        <TButton
          block
          variant="ghost"
          color="neutral"
          class="justify-start"
          :ui="{
            base: 'focus:ring-primary-500 dark:focus:ring-primary-400 min-h-8 bg-neutral-50 ring-1 ring-inset ring-neutral-300 focus:ring-2 dark:bg-neutral-700 dark:ring-neutral-700 dark:hover:!bg-neutral-700',
          }"
        >
          {{ day }}
        </TButton>
      </TFormField>
      <template #content>
        <div class="grid w-full grid-cols-5 gap-0.5 p-3">
          <template v-for="d in days" :key="`day_${d}`">
            <TButton
              :label="`${d}`"
              variant="ghost"
              square
              :ui="{
                base: 'text-center [&>span]:w-full',
              }"
              @click="((day = d), (dayOpen = false))"
            />
          </template>
        </div>
      </template>
    </TPopover>
    <TSeparator />
    <template #footer> </template>
  </Wrapper>
</template>
