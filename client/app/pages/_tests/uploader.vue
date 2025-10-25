<script setup lang="ts">
import { z } from "zod";
import type { FormSubmitEvent } from "#ui/types";
import type {
  FileItem,
  FileStatus,
  UploadEventError,
  UploadEventResponse,
} from "~/types/composables/useUploader";

const {
  files,
  uploading,
  paused,
  progress,
  upload,
  fileBrowser,
  resumeAll,
  pauseAll,
  cancelAll,
  clear,
} = useUploader({
  api: "/test/upload",
  integrity: { check: true },
  multiple: true,
  events: {
    // uploaded: ({ file, response }) => {
    //   log("upload", file, response);
    // },
    // error: ({ file, error }) => {
    //   log("error", file, error);
    // },
    uploaded: (result: UploadEventResponse) => {
      log("upload", result.file, result.response);
    },
    error: (result: UploadEventError) => {
      log("error", result.file, result.error);
    },
    cancelled: (files: Array<FileItem>) => {
      log("cancel", files);
    },
    paused: (files: Array<FileItem>) => {
      log("pause", files);
    },
    resumed: (files: Array<FileItem>) => {
      log("resume", files);
    },
  },
});

const allowDuplicates = ref(false);
const canRename = ref(true);
const canPause = ref(true);
const canCancel = ref(true);
const clearables = ref<Array<FileStatus>>(["pending", "cancelled", "error"]);
const clearableItems = ref<Array<FileStatus>>([
  "pending",
  "uploading",
  "complete",
  "error",
  "paused",
  "cancelled",
]);

const cancelling = computed(() => files.value.some((f) => f.upload.cancelling));
const pausing = computed(() => files.value.some((f) => f.upload.pausing));
const resuming = computed(() => files.value.some((f) => f.upload.resuming));

const schema = z.object({
  files: z.instanceof(FileList).nullable(),
});

type Schema = z.output<typeof schema>;

const state = reactive({ files: null });

const onSubmit = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    upload(2)
      .then((response) => {
        log(response);
        resolve(response);
      })
      .catch((e) => {
        error(e);
        reject(e);
      });
  });
};

watch(allowDuplicates, (val) => {
  if (!val) {
    clear("duplicates");
  }
});
</script>

<template>
  <div class="flex flex-auto flex-col gap-4 p-4">
    <div class="flex flex-auto justify-center">
      <TForm
        :schema
        :state
        :validateOn="[]"
        @submit="onSubmit($event)"
        class="flex flex-col gap-5"
      >
        <div
          class="w-screen-95 flex h-[33rem] max-w-md flex-col rounded border border-neutral-400/25"
        >
          <div class="px-3 py-1">File Upload</div>
          <div
            class="flex items-center justify-end gap-2 bg-neutral-50 dark:bg-neutral-800"
          >
            <div class="flex flex-auto items-center gap-2">
              <TPopover arrow>
                <TButton
                  label="Options"
                  size="xs"
                  variant="ghost"
                  color="neutral"
                />

                <template #content>
                  <div class="flex flex-col gap-2 px-3 py-2">
                    <div class="text-sm font-semibold text-neutral-400">
                      Upload options
                    </div>
                    <TCheckbox
                      label="Allow Duplicates"
                      v-model="allowDuplicates"
                    />
                    <TCheckbox label="Can Rename" v-model="canRename" />
                    <TCheckbox label="Can Pause" v-model="canPause" />
                    <TCheckbox label="Can Cancel" v-model="canCancel" />
                  </div>
                </template>
              </TPopover>
            </div>
            <TButton
              label="Browse Files"
              size="xs"
              variant="ghost"
              color="neutral"
              @click="fileBrowser('*', !allowDuplicates)?.open()"
              :disabled="uploading || paused"
            />
            <template v-if="canPause">
              <TTooltip text="Pause All">
                <TButton
                  icon="tabler:pause"
                  variant="ghost"
                  color="neutral"
                  size="xs"
                  :loading="pausing"
                  :disabled="!files.some((f) => f.status == 'uploading')"
                  @click="pauseAll"
                />
              </TTooltip>
              <TTooltip text="Resume All">
                <TButton
                  icon="tabler:play"
                  variant="ghost"
                  color="neutral"
                  size="xs"
                  :loading="resuming"
                  :disabled="!files.some((f) => f.status == 'paused')"
                  @click="resumeAll"
                />
              </TTooltip>
            </template>
            <template v-if="canCancel">
              <TTooltip text="Cancel All">
                <TButton
                  icon="tabler:player-stop"
                  variant="ghost"
                  color="neutral"
                  size="xs"
                  :loading="cancelling"
                  :disabled="files.length <= 0"
                  @click="cancelAll"
                />
              </TTooltip>
            </template>
            <TPopover>
              <TButton icon="tabler:trash" variant="ghost" color="warning" />
              <template #content>
                <div class="flex flex-col gap-2 px-3 py-2">
                  <TCheckboxGroup v-model="clearables" :items="clearableItems">
                  </TCheckboxGroup>
                  <TSeparator />
                  <TButton
                    label="Cleanup"
                    color="warning"
                    :ui="{ base: '-mx-2 -mb-1 justify-center' }"
                    size="xs"
                    @click="clear(clearables)"
                  />
                </div>
              </template>
            </TPopover>
          </div>

          <TProgress v-model="progress.percent" size="xs" />

          <TransitionGroup
            enter-active-class="transform-gpu transition-all"
            enter-class="opacity-0 translate-x-16 scale-y-50"
            enter-to-class="translate-x-0 scale-y-100 opacity-100"
            leave-active-class="absolute transform-gpu transition-all"
            leave-to-class="origin-[center_top] scale-y-0 opacity-0"
            move-class="transition-all"
            tag="ul"
            class="relative flex-auto overflow-y-auto"
          >
            <li
              v-for="file in files"
              :key="file.id"
              class="block w-full px-3 py-2"
            >
              <TFileUploadItem
                :file="file"
                :canRename
                :canPause
                :canCancel
                class="rounded-md border border-neutral-200 shadow-md dark:border-neutral-700 dark:bg-neutral-800"
              />
            </li>
            <li v-if="files.length <= 0" class="block h-20 w-full">
              <div
                class="flex h-full w-full items-center justify-center text-center text-neutral-400/55"
              >
                No Files Selected
              </div>
            </li>
          </TransitionGroup>
          <TSeparator />
          <div class="flex items-center p-2">
            <TButton
              label="Upload"
              type="submit"
              size="xs"
              :disabled="uploading || paused || files.length <= 0"
              :ui="{ base: 'flex-auto justify-center' }"
            />
          </div>
        </div>
      </TForm>
    </div>
  </div>
</template>
