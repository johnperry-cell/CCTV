<script setup lang="ts">
import type { FileItem } from "~/types/composables/useUploader";
import type { ProgressProps } from "@nuxt/ui";

const props = withDefaults(
  defineProps<{
    file: FileItem;
    canPause?: boolean;
    canCancel?: boolean;
    canRename?: boolean;
  }>(),
  {
    canPause: false,
    canCancel: false,
    canRename: false,
  },
);

const { msToReadable, formatSize } = useUtils();

const rename = ref({
  show: false,
  value: "",
});

const state = computed(() => {
  if (["pending", "uploading"].indexOf(props.file.status) > -1) {
    return null;
  }
  if (props.file.status === "error") {
    return {
      icon: "tabler:alert-triangle",
      message: "File Upload failed!",
      class: "text-red-400",
      progress: "red",
    };
  }

  if (props.file.status === "cancelled") {
    return {
      icon: "tabler:cloud-cancel",
      message: "File Upload cancelled!",
      class: "text-pink-400",
      progress: "pink",
    };
  }

  if (props.file.hashing) {
    return {
      icon: "tabler:loader-2",
      message: "Checking file integrity...",
      class: "animate-spin",
      progress: "lime",
    };
  }

  if (props.file.matchedHash === false) {
    return {
      icon: "tabler:alert-triangle",
      message: "File Upload sucessful with mismatched hash!",
      class: "text-teal-600",
      progress: "teal",
    };
  }

  if (props.file.status == "complete") {
    return {
      icon: props.file.matchedHash ? "tabler:checks" : "tabler:check",
      message: "File Upload sucessful!",
      class: "text-green-600",
      progress: "pine",
    };
  }

  if (props.file.status == "paused") {
    return {
      icon: "tabler:pause",
      message: "File Upload paused!",
      class: "text-amber-400",
      progress: "amber",
    };
  }
});

const progressColor = computed(() => {
  if (props.file.status == "paused") {
    return "amber";
  }

  if (["error", "cancelled"].includes(props.file.status)) {
    return "red";
  }

  if (props.file.status == "complete") {
    return "green";
  }

  return "blue";
});

const openRename = (name: string) => {
  rename.value.value = name;
  rename.value.show = true;
};

watch(
  () => props.file.status == "pending",
  (val) => {
    if (!val) {
      rename.value.show = false;
    }
  },
);
</script>

<template>
  <div class="relative px-3 py-1">
    <div class="flex items-center justify-between gap-2">
      <div class="line-clamp-1 flex-auto">
        {{ file.name }}
      </div>
      <TButton
        v-if="['uploading', 'paused'].includes(file.status) && canCancel"
        icon="tabler:player-stop"
        variant="ghost"
        color="neutral"
        size="xs"
        :loading="file.upload.cancelling"
        :disabled="rename.show"
        @click="file.cancel()"
      />

      <template v-if="file.status == 'uploading' && canPause">
        <TButton
          icon="tabler:pause"
          variant="ghost"
          color="neutral"
          size="xs"
          :loading="file.upload.pausing"
          :disabled="rename.show"
          @click="file.pause()"
        />
      </template>
      <template v-if="file.status == 'paused' && canPause">
        <TButton
          icon="tabler:play"
          variant="ghost"
          color="neutral"
          size="xs"
          :loading="file.upload.resuming"
          :disabled="rename.show"
          @click="file.resume()"
        />
      </template>
      <TButton
        v-if="file.status == 'pending' && canRename"
        icon="tabler:pencil"
        variant="ghost"
        color="neutral"
        size="xs"
        :disabled="rename.show"
        @click="openRename(file.name)"
      />
      <TButton
        v-if="
          ['cancelled', 'error', 'pending', 'complete'].includes(file.status)
        "
        icon="tabler:x"
        variant="ghost"
        color="neutral"
        size="xs"
        :disabled="rename.show"
        @click="file.remove(true)"
      />
    </div>

    <div class="flex items-center justify-between gap-2 text-xs">
      <span>{{ formatSize(file.file.size) }}</span>
      <span v-if="file.status === 'uploading' && file.upload.progress < 100">
        {{ `${formatSize(file.upload.speed)}/s` }}
      </span>
      <span
        v-if="file.status == 'cancelled' && false"
        class="text-error-500 font-semibold"
      >
        cancelled!
      </span>
    </div>

    <div class="flex items-center gap-2">
      <TProgress
        v-model="file.upload.progress"
        :color="(state?.progress ?? 'secondary') as any"
      />
      <div class="flex size-4 items-center justify-center">
        <template v-if="!!state">
          <TTooltip :text="state.message">
            <TIcon :name="state.icon" class="size-4" :class="state.class" />
          </TTooltip>
        </template>

        <TIcon
          v-if="['uploading', 'pending'].includes(file.status)"
          :name="
            file.status == 'pending'
              ? 'line-md:uploading'
              : 'line-md:uploading-loop'
          "
          class="size-4"
        />
      </div>
    </div>

    <table v-if="false">
      <tbody class="[&>tr>td]:p-2">
        <tr>
          <td>speed</td>
          <td>
            {{ `${formatSize(file.upload.speed)}/s` }}
          </td>
        </tr>
        <tr>
          <td>ellapsed</td>
          <td>
            {{ `${msToReadable(file.upload.ellapsed).str}` }}
          </td>
        </tr>
        <tr>
          <td>loaded</td>
          <td>
            {{ `${formatSize(file.upload.loaded)}` }}
          </td>
        </tr>
        <tr>
          <td>timeRemaining</td>
          <td>
            {{ `${msToReadable(file.upload.timeRemaining).str}` }}
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="rename.show"
      class="absolute inset-0 rounded-lg p-4 backdrop-blur"
    >
      <div class="flex items-center gap-2">
        <TInput
          v-model="rename.value"
          :ui="{
            root: 'flex-auto',
          }"
          @keydown.enter.prevent
          @keyup.prevent.stop.enter="
            ((file.name = rename.value), (rename.show = false))
          "
        />
        <TButton
          label="ok"
          @click="((file.name = rename.value), (rename.show = false))"
        />
        <TButton label="cancel" @click="rename.show = false" />
      </div>
    </div>
  </div>
</template>
