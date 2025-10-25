<script setup lang="ts">
import type { SearcherOptions } from "~/types/composables/useSearcher";
import type { PermissionItem } from "~/types/models/permission";

const { dashToHuman } = useUtils();

const props = defineProps<{
  searchApi: string;
  viewSelected?: boolean;
  locked?: Array<string>;
  compact?: boolean;
  striped?: boolean;
  searchOptions?: SearcherOptions;
  viewOnly?: boolean;
}>();

const abortController = new AbortController();

const {
  pagination,
  params,
  loading: sLoading,
  search,
} = useSearcher<{ search: string }>({
  api: props.searchApi,
  limit: 10,
  appendToUrl: false,
  type: "custom",
  signal: abortController.signal,
  onSearch: (response) => {
    permissions.value = response.data.data as Array<PermissionItem>;
    isNone.value = modelHasNone();
  },
});

const model = defineModel({
  type: Array as PropType<Array<PermissionItem>>,
  default: () => [],
});
const loading = defineModel("loading", {
  default: false,
  type: Boolean,
});
const loadingMessage = defineModel("loadingMessage", {
  default: "",
  type: String,
});
const error = defineModel("error", {
  default: null,
  type: String,
});

const permissions = ref<Array<PermissionItem>>([]);
const isNone = ref(false);
const showSearch = ref(false);
const open = ref(false);

const nameClass = (permission: PermissionItem): string => {
  let c = "";
  if (loading || isLocked(permission)) {
    c = "text-neutral-400";
  }
  if (!loading && !isLocked(permission)) {
    c = "text-neutral-700 dark:text-neutral-200";
  }
  return c;
};

const modelHasNone = (): boolean => {
  return model.value.length <= 0 && (props.locked?.length ?? 0) <= 0;
};

const onNone = () => {
  model.value = [];
  isNone.value = true;
};

const isSelected = (permission: PermissionItem) => {
  return !!model.value.find((p) => p.id === permission.id);
};

const onCheck = (permission: PermissionItem) => {
  if (isSelected(permission)) {
    model.value = model.value.filter((p) => p.id !== permission.id);
  } else {
    model.value.push(permission);
  }
};

const isLocked = (permission: PermissionItem) => {
  return props.locked?.includes(permission.id);
};

watch(sLoading, (val) => {
  loading.value = val;
});

watch(
  [model, () => props.locked],
  () => {
    isNone.value = modelHasNone();
  },
  { deep: true },
);

onMounted(() => {
  search();
});

onBeforeUnmount(() => {
  abortController.abort();
});
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center justify-between gap-2">
      <TTypography
        variant="md"
        class="flex items-center gap-2 font-semibold text-neutral-700 dark:text-neutral-200"
      >
        <span>
          Permissions
          <template v-if="!viewOnly">{{ `(${model.length ?? 0})` }}</template>
        </span>
        <TIcon v-if="loading" name="tabler:loader-2" class="animate-spin" />
      </TTypography>
      <div class="flex items-center gap-2">
        <TPopover v-if="viewSelected && !viewOnly" v-model:open="open">
          <TButton
            icon="tabler:list"
            size="sm"
            variant="ghost"
            color="neutral"
          />
          <template #content>
            <div
              class="w-screen-95 flex max-h-64 min-h-64 max-w-sm flex-col gap-2 px-3 py-2"
            >
              <div
                class="flex items-center justify-between border-b border-neutral-400/25 font-semibold"
              >
                Selected Permissions
                <TButton
                  icon="tabler:x"
                  variant="ghost"
                  color="neutral"
                  @click="open = false"
                />
              </div>
              <div
                class="scrollbar-thin flex flex-auto flex-col gap-2 overflow-auto overscroll-contain"
              >
                <template
                  v-for="selected in model"
                  :key="`selected_${selected.id}`"
                >
                  <div
                    class="jsutify-between flex items-center gap-2"
                    :class="{
                      'px-1 odd:bg-neutral-400/10': viewOnly,
                    }"
                  >
                    <span class="line-clamp-1 flex-auto text-sm">
                      {{ dashToHuman(selected.name) }}
                    </span>
                    <TButton
                      v-if="!viewOnly"
                      icon="tabler:x"
                      variant="ghost"
                      color="error"
                      @click="onCheck(selected)"
                    />
                  </div>
                </template>
                <div
                  v-if="!model.length"
                  class="flex h-full flex-auto flex-col items-center justify-center gap-2 text-neutral-400"
                >
                  <TIcon
                    name="tabler:database"
                    class="h-6 w-6 text-neutral-400 dark:text-neutral-500"
                  />
                  <TTypography
                    variant="sm"
                    class="text-center text-neutral-900 dark:text-white"
                  >
                    No permission selected
                  </TTypography>
                </div>
              </div>
            </div>
          </template>
        </TPopover>
        <TButton
          icon="tabler:search"
          size="sm"
          variant="ghost"
          color="neutral"
          :disabled="loading"
          :class="{
            'text-primary-400 dark:text-primary-400': showSearch,
          }"
          @click="showSearch = !showSearch"
        />
        <label
          for="none_chk"
          class="flex select-none items-center gap-2"
          :class="{
            'cursor-not-allowed text-neutral-400':
              (locked?.length ?? 0) > 0 || loading || viewOnly,
          }"
        >
          <TCheckbox
            id="none_chk"
            v-model="isNone"
            :disabled="loading || (locked?.length ?? 0) > 0 || viewOnly"
            @change="onNone"
          />
          <span>None</span>
        </label>
      </div>
    </div>
    <TCollapse v-model="showSearch">
      <TFormField
        name="permission-search"
        class="relative flex-auto p-1 [&>*]:w-full"
      >
        <TInput
          placeholder="Search..."
          class="w-full flex-auto"
          v-model="params.search"
          name="permission-search"
          autocompleete="off"
          :disabled="loading"
          @keydown.enter.prevent="search()"
        >
          <template #trailing>
            <TButton
              icon="tabler:search"
              :loading
              :disabled="loading"
              color="neutral"
              size="md"
              variant="link"
              class="px-3"
              @click="search()"
            />
          </template>
        </TInput>
      </TFormField>
    </TCollapse>
    <div
      class="scrollbar-thin flex flex-auto flex-col divide-y divide-neutral-400/25 overflow-auto overscroll-contain pr-3 transition-all"
      :class="{
        '-mt-4': !showSearch,
      }"
    >
      <template v-for="permission in permissions" :key="permission.id">
        <label
          :for="permission.id"
          class="flex select-none items-center gap-2 px-1 transition-colors"
          :class="{
            'odd:bg-neutral-500/10': striped,
            'py-1': compact,
            'py-2': !compact,
            'cursor-not-allowed':
              (loading || isLocked(permission)) && !viewOnly,
            '!bg-primary-400/10 odd:!bg-primary-400/15': isLocked(permission),
            'cursor-pointer hover:bg-neutral-400/10':
              !loading && !isLocked(permission) && !viewOnly,
          }"
        >
          <div class="flex-auto">
            <TTypography
              variant="sm"
              class="font-semibold"
              :class="nameClass(permission)"
            >
              {{ dashToHuman(permission.name) }}
            </TTypography>
            <TTypography
              v-if="!compact"
              variant="sm"
              class="text-sm text-neutral-500 dark:text-neutral-400"
            >
              {{ permission.description }}
            </TTypography>
          </div>
          <TCheckbox
            v-if="!viewOnly"
            :modelValue="isSelected(permission) || isLocked(permission)"
            :id="permission.id"
            :disabled="loading || isLocked(permission)"
            :ui="{ base: 'h-5 w-5' }"
            @change="onCheck(permission)"
          />
        </label>
      </template>
      <div
        v-if="!loading && !permissions.length"
        class="flex flex-col items-center justify-center gap-2 text-neutral-400"
      >
        <TIcon
          name="tabler:database"
          class="h-6 w-6 text-neutral-400 dark:text-neutral-500"
        />
        <TTypography
          variant="sm"
          class="text-center text-neutral-900 dark:text-white"
        >
          No permissions found
        </TTypography>
      </div>
    </div>

    <div class="flex items-center justify-end">
      <TPagination
        v-model:page="pagination.page"
        :total="pagination.total"
        :pageCount="pagination.limit"
        :disabled="loading"
        size="xs"
      />
    </div>
  </div>
</template>
