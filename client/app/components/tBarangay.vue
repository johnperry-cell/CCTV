<script setup lang="ts">
import { TFormField } from "#components";
import type {
  RegionType,
  ProvinceType,
  CityType,
  BarangayType,
} from "~/types/models/address";
import type { HasKey } from "~/types";
import AddressSelect from "./tBarangay/addressSelect.vue";

const model = defineModel({
  type: Object as PropType<BarangayType | undefined>,
  default: undefined,
});

const { $api } = useNuxtApp();

const tab = ref("region");
const open = ref(false);
const popover = ref<InstanceType<typeof TFormField>>();
const w = ref(0);
const loading = ref(false);
const loaders = ref<Record<string, boolean>>({
  region: false,
  province: false,
  city: false,
  barangay: false,
});

const regions = ref<Array<RegionType>>([]);
const provinces = ref<Array<ProvinceType>>([]);
const cities = ref<Array<CityType>>([]);
const barangays = ref<Array<BarangayType>>([]);

const selected = ref<
  HasKey & {
    region?: RegionType;
    province?: ProvinceType;
    city?: CityType;
  }
>({
  region: undefined,
  province: undefined,
  city: undefined,
});

const label = computed(() => {
  return [
    selected.value.region?.name,
    selected.value.province?.name,
    selected.value.city?.name,
    model.value?.name,
  ]
    .filter(Boolean)
    .join(", ");
});

const tabs = computed(() => [
  {
    label: "Region",
    disabled: false,
    value: "region",
  },
  {
    label: "Province",
    disabled: !selected.value.region,
    value: "province",
  },
  {
    label: "City",
    disabled: !selected.value.province,
    value: "city",
  },
  {
    label: "Barangay",
    disabled: !selected.value.city,
    value: "barangay",
  },
]);

const observer = new ResizeObserver((entries) => {
  const first = entries[0];
  if (first) {
    w.value = first.contentRect.width;
  }
});

const loadAddress = () => {
  loading.value = true;
  Object.keys(loaders.value).forEach((k) => (loaders.value[k] = true));
  let uri = "/address/initial/city/141102000";
  if (model.value) {
    uri = `/address/initial/barangay/${model.value.code}`;
  }
  $api
    .get(uri)
    .then((response) => {
      regions.value = response.data.regions as Array<RegionType>;
      provinces.value = response.data.provinces as Array<ProvinceType>;
      cities.value = response.data.cities as Array<CityType>;
      barangays.value = response.data.barangays as Array<BarangayType>;

      const loc = response.data.city ?? response.data.barangay;

      selected.value.region = regions.value.find(
        (r) => r.code === loc.regionCode,
      );
      selected.value.province = provinces.value.find(
        (p) => p.code === loc.provinceCode,
      );
      selected.value.city = cities.value.find(
        (c) => c.code === (loc.cityCode ?? loc.code),
      );

      tab.value = "barangay";
    })
    .finally(() => {
      loading.value = false;
      Object.keys(loaders.value).forEach((k) => (loaders.value[k] = false));
    });
};

const selectRegion = (region: RegionType) => {
  selected.value.region = region;

  provinces.value = [];
  cities.value = [];
  barangays.value = [];

  selected.value.province = undefined;
  selected.value.city = undefined;
  model.value = undefined;
  tab.value = "province";
};

const selectProvince = (province: ProvinceType) => {
  selected.value.province = province;

  cities.value = [];
  barangays.value = [];

  selected.value.city = undefined;
  model.value = undefined;
  tab.value = "city";
};

const selectCity = (city: CityType) => {
  selected.value.city = city;

  barangays.value = [];

  model.value = undefined;
  tab.value = "barangay";
};

const selectBarangay = (barangay: BarangayType) => {
  model.value = barangay;
  open.value = false;
};

onMounted(() => {
  loadAddress();
  nextTick(() => {
    observer.observe(popover.value?.$el);
  });
});

onBeforeUnmount(() => {
  observer.disconnect();
});
</script>

<template>
  <TPopover
    v-model:open="open"
    :popper="{ adaptive: true }"
    class="col-span-full"
  >
    <slot
      :label="label"
      :selected="Object.assign(selected, { barangay: model })"
    >
      <TFormField
        ref="popover"
        label="Region, Province, City, Barangay"
        name="barangay"
        class="flex-auto"
        required
      >
        <TInput
          v-model="label"
          type="button"
          icon="tabler:map-pin"
          class="w-full"
          :loading
          :ui="{
            base: 'max-w-full text-start',
            leading: 'text-dimmed',
          }"
        >
          <template v-if="!selected.region && !loading" #leading>
            {{ "Select Region" }}
          </template>
        </TInput>
      </TFormField>
    </slot>
    <template #content>
      <TCard
        :ui="{
          root: 'h-screen-95 max-h-96 overflow-hidden',
          header: 'p-0 sm:p-0',
          body: 'overflow-auto p-0 sm:p-0',
        }"
        :style="{ width: `${w}px` }"
      >
        <template #header>
          <TTabs
            v-model="tab"
            :items="tabs"
            :content="false"
            variant="link"
            size="sm"
            :ui="{ root: 'flex-auto', trigger: 'grow' }"
          />
        </template>
        <AddressSelect
          v-if="tab == 'region'"
          api="/address/regions"
          v-model:items="regions"
          v-model="selected.region"
          class="px-3 py-2"
          @select="selectRegion"
        >
          <template #option="{ option, selected }">
            <div class="flex flex-auto flex-col text-start">
              <TTypography variant="sm">
                {{ option.name }}
              </TTypography>
              <TTypography variant="xs" class="leading-none text-gray-400">
                {{ option.regionName }}
              </TTypography>
            </div>
            <TIcon v-if="selected" name="tabler:check" />
          </template>
        </AddressSelect>
        <AddressSelect
          v-if="tab == 'province' && selected.region"
          :api="`/address/provinces/${selected.region?.code}`"
          :items="provinces"
          v-model="selected.province"
          class="px-3 py-2"
          @select="selectProvince"
        />
        <AddressSelect
          v-if="tab == 'city' && selected.province"
          :api="`/address/cities/${selected.province?.code}`"
          :items="cities"
          v-model="selected.city"
          class="px-3 py-2"
          @select="selectCity"
        />
        <AddressSelect
          v-if="tab == 'barangay' && selected.city"
          :api="`/address/barangays/${selected.city?.code}`"
          :items="barangays"
          v-model="selected.barangay"
          class="px-3 py-2"
          @select="selectBarangay"
        >
          <template #option="{ option, selected }">
            <div class="flex flex-auto flex-col text-start">
              <TTypography variant="sm">
                {{ option.name }}
              </TTypography>
              <TTypography variant="xs" class="leading-none text-gray-400">
                {{ option.oldName }}
              </TTypography>
            </div>
            <TIcon v-if="selected" name="tabler:check" />
          </template>
        </AddressSelect>
      </TCard>
    </template>
  </TPopover>
</template>
