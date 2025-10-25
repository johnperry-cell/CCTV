<script setup lang="ts">
import type { LogData } from "~/types/models/logs";
import JsonEditorVue from "json-editor-vue";
import Badger from "~/pages/settings/roles/roleBadge.vue";

const { $dayjs } = useNuxtApp();
const colorMode = useColorMode();

const props = defineProps<{
  data: LogData;
  levelMap: Record<number, string>;
  colors: Record<number, string>;
}>();

const emit = defineEmits(["close"]);

const showOld = ref(true);
</script>

<template>
  <TCard
    :ui="{
      body: 'flex flex-col',
      footer: 'flex items-center justify-end gap-2',
    }"
  >
    <template #header>
      <div class="flex-auto font-semibold">Log Info</div>
      <TButton
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        @click="emit('close')"
      />
    </template>
    <div class="space-y-4 overflow-y-auto pr-2">
      <div class="space-y-1">
        <h6 class="font-medium">Actor</h6>
        <div
          class="flex items-center gap-3 rounded-md border border-neutral-400 p-3 dark:border-neutral-500"
        >
          <div class="">
            <span class="font-medium"> {{ data.actor }}</span>
            <div
              class="flex items-center gap-3 *:text-neutral-400 *:dark:text-neutral-300"
            >
              <span>{{ data.user?.username }}</span>
              <template v-if="data.user?.email">
                <span class="text-dimmed">|</span>
                <span class="text-dimmed">{{ data.user.email }}</span>
              </template>
            </div>
          </div>
        </div>
      </div>
      <div class="space-y-1">
        <h6 class="font-medium">Time</h6>
        <p
          class="rounded-md border border-neutral-400 p-2 dark:border-neutral-500"
        >
          {{ $dayjs(data.date).format("YYYY-MM-DD HH:mm:ss") }}
        </p>
      </div>
      <div class="space-y-1">
        <h6 class="font-medium">Module</h6>
        <p
          class="rounded-md border border-neutral-400 p-2 dark:border-neutral-500"
        >
          {{ data.module }}
        </p>
      </div>
      <div class="space-y-1">
        <h6 class="font-medium">Level</h6>
        <p
          class="rounded-md border border-neutral-400 p-2 dark:border-neutral-500"
        >
          <Badger :color="colors[data.level]!">
            <span class="text-base">
              {{ levelMap[data.level] }}
            </span>
          </Badger>
        </p>
      </div>

      <div class="space-y-1">
        <h6 class="font-medium">Header/Summary</h6>
        <p
          class="rounded-md border border-neutral-400 p-2 dark:border-neutral-500"
        >
          {{ data.action }}
        </p>
      </div>
      <div class="flex flex-auto flex-col space-y-2">
        <div class="flex items-center gap-2">
          <TButton
            label="Old Data"
            :variant="showOld ? 'solid' : 'ghost'"
            @click="showOld = true"
          />
          <TButton
            label="New Data"
            :variant="!showOld ? 'solid' : 'ghost'"
            @click="showOld = false"
          />
        </div>
        <JsonEditorVue
          class="flex-auto"
          :modelValue="showOld ? data.old_data : data.new_data"
          readOnly
        />
      </div>
    </div>
    <template #footer>
      <TButton label="Close" variant="outline" @click="emit('close')" />
    </template>
  </TCard>
</template>
