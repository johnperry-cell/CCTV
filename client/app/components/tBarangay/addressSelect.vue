<script setup lang="ts">
import type {
  RegionType,
  ProvinceType,
  CityType,
  BarangayType,
} from "~/types/models/address";
import Fuse from "fuse.js";

type AddressType = RegionType | ProvinceType | CityType | BarangayType;

const props = defineProps<{
  api: string;
}>();
const emit = defineEmits(["select"]);

const { $api } = useNuxtApp();

const selected = defineModel<AddressType>();
const items = defineModel<Array<AddressType>>("items", { default: [] });

const container = ref<HTMLElement>();
const loading = ref(false);
const search = ref("");
const fuseSearch = new Fuse(items.value, {
  shouldSort: true,
  threshold: 0.6,
  location: 0,
  distance: 100,
  minMatchCharLength: 1,
  keys: ["name", "oldName", "regionName"],
});

const searched = computed(() => {
  const result = fuseSearch.search(search.value);

  container.value?.scrollTo({
    top: 0,
  });
  return !search.value.trim() ? items.value : result.map((r) => r.item);
});

const loadAddressItems = () => {
  loading.value = true;
  $api
    .get(props.api)
    .then((response) => {
      items.value = response.data as Array<AddressType>;
      fuseSearch.setCollection(items.value);
    })
    .finally(() => {
      loading.value = false;
    });
};

onMounted(() => {
  if (items.value.length == 0) {
    loadAddressItems();
  }
});
</script>

<template>
  <div ref="container" class="flex max-h-full flex-col overflow-auto">
    <TInput
      v-model="search"
      icon="tabler:search"
      :loading
      :ui="{
        root: 'bg-default sticky -top-2 w-full p-1',
        base: ['max-w-full'],
      }"
    />
    <template v-for="item in searched" :key="item.code">
      <TButton
        variant="ghost"
        color="neutral"
        :ui="{ base: 'rounded-none' }"
        :class="{
          'dark:!bg-primary-700': item.code == selected?.code,
        }"
        @click="emit('select', item)"
      >
        <slot
          name="option"
          :option="item"
          :selected="item.code == selected?.code"
        >
          <div class="flex-auto text-start">
            {{ item.name }}
          </div>
          <TIcon v-if="item.code == selected?.code" name="tabler:check" />
        </slot>
      </TButton>
    </template>
  </div>
</template>
