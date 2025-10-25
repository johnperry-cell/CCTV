import type { AxiosResponse } from "axios";
import defu from "defu";
import type {
  UploadOptions,
  FileItem,
  FileStatus,
  UploadEventResponse,
  UploadEventError,
} from "~/types/composables/useUploader";
import { UploadQueue } from "./uploader/uploadQueue";

export const useUploader = (
  options?: UploadOptions | ComputedRef<UploadOptions>,
) => {
  const { uniqid } = useUtils();
  const { $api, $apiExcludeFromNotif } = useNuxtApp();

  const files = ref<Array<FileItem>>([]);
  const retries = ref<Record<string, number>>({});
  const completed = ref<Array<string>>([]);

  const uploading = computed(() =>
    files.value.some((file) => file.status === "uploading"),
  );

  const paused = computed(() =>
    files.value.some((file) => file.status === "paused"),
  );

  const progress = computed(() => {
    const f = files.value.filter((f) => !_isCancelled(f));
    const total = f.map((f) => f.file.size).reduce((a, b) => a + b, 0);
    const loaded = f.map((f) => f.upload.loaded).reduce((a, b) => a + b, 0);
    return {
      loaded,
      total,
      percent: Math.round((loaded / total) * 100) || 0,
    };
  });

  const _defaults = {
    multiple: false,
    sleep: {
      long: 1500,
      short: 250,
      interval: 0,
    },
    uprate: 250,
    concurrency: 1,
    maxRetries: 3,
    dispose: true,
    integrity: {
      check: false,
      resultKey: "sha256",
    },
  } as UploadOptions;

  const _options = computed<UploadOptions>(() =>
    defu(toValue(options), _defaults),
  );

  const _concurrency = ref(_options.value.concurrency);
  const _queue = new UploadQueue(_concurrency.value);

  const _isDone = (file: FileItem) => ["complete"].includes(file.status);
  const _isActive = (file: FileItem) =>
    ["uploading", "paused"].includes(file.status);

  const _isFailed = (file: FileItem) => ["error"].includes(file.status);
  const _isCancelled = (file: FileItem) => ["cancelled"].includes(file.status);

  const _checkIfDone = (): Promise<AxiosResponse[]> => {
    return new Promise(async (resolve, reject) => {
      while (files.value.some(_isActive)) {
        await _sleeper(100);
      }

      const filtered = files.value
        .filter((f) => !completed.value.includes(f.id))
        .filter((f) => !_isCancelled(f));

      const errored = filtered.some(_isFailed);
      const results = filtered.map((f) => f.upload.task?.promise);

      completed.value.push(...filtered.map((f) => f.id));

      errored
        ? reject(results)
        : resolve(results as unknown as AxiosResponse[]);
    });
  };

  const _queueFiles = (files: Array<FileItem>) => {
    for (const file of files) {
      _queueFile(file);
    }
  };

  const _queueFile = (file: FileItem) => {
    file.status = "uploading";
    file.upload.abortController = new AbortController();
    const { promise, resolve, reject } = Promise.withResolvers<AxiosResponse>();
    file.upload.task = { promise, resolve, reject };
    _queue.add(async () => await _uploadFile(file).then(resolve).catch(reject));
  };

  const _uploadFile = async (item: FileItem): Promise<AxiosResponse> => {
    let result;
    let retry_id = "";

    _upRate(item);

    while (
      item.status === "uploading" &&
      !!files.value.find((f) => f.id === item.id)
    ) {
      try {
        await _sleeper(_options.value.sleep?.short!);
        const interval = _options.value.sleep?.interval!;
        const formData = _createFormData(item);
        retry_id = `${item.id}_${item.state?.part}`;

        if (
          item.state?.part &&
          item.state.part > 0 &&
          !retries.value.hasOwnProperty(retry_id)
        ) {
          Object.assign(retries.value, { [retry_id]: 0 });
        }

        result = await $api.post(_options.value.api, formData, {
          signal: item.upload.abortController!.signal,
        });

        const file = formData.get("file") as File;
        if (!!file) {
          item.upload.loaded += file.size;
        }

        if (result.data.part % interval == 0) {
          await _sleeper(_options.value.sleep?.long!);
        }

        if (result.status === 200 || result.status === 201) {
          item.state.uid = result.data.uid ?? item.state?.uid;
          item.state.part = result.data.part;
          item.state.parts = result.data.parts ?? item.state?.parts;
          item.state.length = result.data.length;
          item.state.progress = result.data.progress;
          item.status = result.data.status;
        }
      } catch (e: any) {
        const status = e.response?.status;

        if (
          status === 429 &&
          retries.value[retry_id] < _options.value.maxRetries!
        ) {
          const retryAfter =
            parseInt(e.response.headers.get("Retry-After")) || 45;
          await _sleeper(retryAfter * 1000);
          retries.value[retry_id]++;
          continue;
        }

        if (e.name == "CanceledError" || e.name == "AbortError") {
          item.status = "cancelled";
          break;
        }
        item.status = "error";
        result = e;
        _options.value.events?.error?.({
          file: item,
          error: result,
        } as UploadEventError);
        break;
      }
    }

    item.upload.speed = 0;
    clearTimeout(item.upload.timer!);
    if (_isDone(item)) {
      item.upload.progress = 100;
      _checkFileIntegrity(item, result);
      _options.value.events?.uploaded?.({
        file: item,
        response: result,
      } as UploadEventResponse);
    }

    return result!;
  };

  const _checkFileIntegrity = async (item: FileItem, result: AxiosResponse) => {
    const hash = _options.value.integrity
      ?.resultKey!.split(".")
      .reduce((a, b) => a[b], result.data);
    if (_options.value.integrity?.check && hash) {
      item.hashing = true;
      const hashed = await _getHash("SHA-256", item.file);
      item.matchedHash = hashed == hash;
      item.hashing = false;
    }
  };

  const _getHash = async (algorithm: string, data: any) => {
    const main = async (msgUint8: BufferSource) => {
      const hashBuffer = await crypto.subtle.digest(algorithm, msgUint8);
      const hashArray = Array.from(new Uint8Array(hashBuffer));
      return hashArray.map((b) => b.toString(16).padStart(2, "0")).join("");
    };

    if (data instanceof Blob) {
      const arrayBuffer = await data.arrayBuffer();
      const msgUint8 = new Uint8Array(arrayBuffer);
      return await main(msgUint8);
    }
    const encoder = new TextEncoder();
    const msgUint8 = encoder.encode(data);
    return await main(msgUint8);
  };

  const _upRate = (item: FileItem) => {
    if (item.status == "uploading") {
      item.upload.start = Date.now();
      item.upload.timer = setInterval(async () => {
        item.upload.ellapsed = Date.now() - item.upload.start;
        item.upload.speed = item.upload.loaded / (item.upload.ellapsed / 1000);
        const remaining =
          ((item.file.size - item.upload.loaded) / item.upload.speed) * 1000;
        if (isFinite(remaining)) {
          item.upload.timeRemaining = remaining;
        }

        const progress = (item.upload.loaded / item.file.size) * 100;
        const previous = item.upload.progress * 1;
        for (let i = previous; i <= progress; i += 1) {
          item.upload.progress = i;
          if (
            item.file.size >= (item.state?.length ?? 0) &&
            item.state?.parts! > 5
          ) {
            await _sleeper(50);
          }
        }
      }, _options.value.uprate);
    }
  };

  const _getFilePart = (file: File, part: number, chunkSize: number) => {
    const start = (part - 1) * chunkSize;
    const end = part * chunkSize;
    return file.slice(start, end);
  };

  const _sleeper = async (ms: number) => {
    return new Promise((resolve) => setTimeout(resolve, ms));
  };

  const _iterable = (obj: any): boolean => {
    if (obj == null || obj === undefined || typeof obj === "string") {
      return false;
    }
    return typeof obj[Symbol.iterator] === "function";
  };

  const _fileExists = (file: File, except?: string): FileItem | undefined => {
    return files.value.find(
      (f) =>
        f.file.name == file.name &&
        f.file.size == file.size &&
        f.file.type == file.type &&
        (except ? f.id != except : true) &&
        !f.duplicate,
    );
  };

  const _createUploadItem = (file: File): FileItem => {
    const id = uniqid("file_");
    return {
      id,
      file,
      name: file.name,
      status: "pending",
      hashing: false,
      upload: {
        timer: null,
        speed: 0,
        start: 0,
        ellapsed: 0,
        loaded: 0,
        timeRemaining: 0,
        progress: 0,
        pausing: false,
        resuming: false,
        cancelling: false,
        abortController: new AbortController(),
      },
      state: {
        uid: undefined,
        part: 1,
        parts: 0,
        length: 0,
        progress: 0,
      },
      duplicate: _fileExists(file),
      remove: (force: boolean = false) => removeFile(id, force),
      pause: () => pause([id]),
      resume: () => resume([id]),
      cancel: () => cancel([id]),
    };
  };

  const _createFormData = (item: FileItem) => {
    const formData = new FormData();
    if (!!item.state?.uid) {
      formData.append("uid", item.state.uid);
      formData.append(
        "file",
        _getFilePart(item.file, item.state.part, item.state.length),
      );
    } else {
      formData.append("name", item.file.name);
      formData.append("rename", item.name);
      formData.append("size", item.file.size.toString());
    }
    return formData;
  };

  const _abortUploading = () => {
    files.value
      .filter((f) => f.status === "uploading" && f.upload.abortController)
      .forEach((f) => f.upload.abortController!.abort());
  };

  const fileBrowser = (accept: string = "*", unique: boolean = true) => {
    if (!uploading.value) {
      const { openFileBrowser } = useFileBrowser({
        accept,
        multiple: _options.value.multiple,
        onChange: (e: Event) => {
          if (!!e.target) {
            addFile((e.target as HTMLInputElement).files!, unique);
          }
        },
      });

      return {
        open: openFileBrowser,
      };
    }
  };

  const addFile = (
    file: File | Array<File> | FileList,
    unique: boolean = false,
  ): Array<string> => {
    if (uploading.value) {
      return files.value.map((file) => file.id);
    }

    if (!_options.value.multiple) {
      files.value = [];
    }

    if (file instanceof File) {
      files.value.push(_createUploadItem(file));
    } else if (_iterable(file)) {
      const fs = Array.from(file);
      if (_options.value.multiple) {
        fs.forEach((f: File) => {
          if (!unique || (unique && !_fileExists(f))) {
            files.value.push(_createUploadItem(f));
          }
        });
      } else {
        files.value.push(_createUploadItem(fs[0]));
      }
    }
    return files.value.map((file) => file.id);
  };

  const removeFile = (id: string, force: boolean = false) => {
    if (!uploading.value || force) {
      files.value = files.value.filter((file) => {
        if (file.status === "uploading") {
          file.upload.abortController?.abort();
        }
        return file.id !== id;
      });
    }
    return files.value.map((file) => file.id);
  };

  const upload = async (concurrency?: number): Promise<AxiosResponse[]> => {
    if (files.value.filter((f) => f.status == "pending").length > 0) {
      if (concurrency && concurrency > 0) {
        _concurrency.value = concurrency;
        _queue.setConcurrency(concurrency);
      }

      const tasks = files.value.filter((file) =>
        ["pending"].includes(file.status),
      );

      _queueFiles(tasks);

      return _checkIfDone();
    }
    return Promise.reject("No files to upload");
  };

  const pause = async (ids: Array<string>) => {
    try {
      const toPause = files.value.filter(
        (f) => ids.includes(f.id) && !_isDone(f),
      );
      const uids = toPause
        .filter((f) => f.state.uid && f.status == "uploading")
        .map((f) => f.state.uid);
      if (uids.length > 0) {
        toPause.forEach((f) => (f.upload.pausing = true));
        await $api.put("upload/pause", { uids }).finally(() => {
          toPause.forEach((f) => (f.upload.pausing = false));
        });
      }

      toPause.forEach((f) => {
        f.status = "paused";
        clearTimeout(f.upload.timer!);
        f.upload.speed = 0;
      });

      _options.value.events?.paused?.(toPause);
    } catch (e) {
      error("Pause failed:", e);
    }
  };

  const resume = async (ids: Array<string>) => {
    try {
      const toResume = files.value.filter(
        (f) => ids.includes(f.id) && f.status == "paused",
      );
      const uids = toResume.filter((f) => f.state.uid).map((f) => f.state.uid);

      if (uids.length > 0) {
        toResume.forEach((f) => (f.upload.resuming = true));
        await $api.put("upload/resume", { uids }).finally(() => {
          toResume.forEach((f) => (f.upload.resuming = false));
        });
      }

      toResume.forEach((f) => {
        f.status = "pending";
        f.upload.abortController = new AbortController();
      });

      _queueFiles(toResume);
      _options.value.events?.resumed?.(toResume);
    } catch (e) {
      error("Resume failed:", e);
    }
  };

  const cancel = async (ids: Array<string>) => {
    try {
      const toCancel = files.value.filter(
        (f) => ids.includes(f.id) && _isActive(f),
      );
      const uids = toCancel.filter((f) => f.state.uid).map((f) => f.state.uid);
      if (uids.length > 0) {
        toCancel.forEach((f) => (f.upload.cancelling = true));
        await $api.put("upload/cancel", { uids }).finally(() => {
          toCancel.forEach((f) => {
            f.upload.cancelling = false;
            f.upload.abortController?.abort();
            f.status = "cancelled";
            f.upload.speed = 0;
            clearTimeout(f.upload.timer!);
          });
        });
      }
      toCancel.forEach((f) => (f.status = "cancelled"));

      _options.value.events?.cancelled?.(toCancel);
    } catch (e) {
      error("Cancel failed:", e);
    }
  };

  const pauseAll = async () => pause(files.value.map((f) => f.id));

  const resumeAll = async () => resume(files.value.map((f) => f.id));

  const cancelAll = async () => cancel(files.value.map((f) => f.id));

  const clear = (
    clearMode: Array<FileStatus> | "all" | "duplicates" = [
      "pending",
      "cancelled",
      "error",
    ],
  ) => {
    if (clearMode === "all") {
      _abortUploading();
      files.value = [];
      return;
    }

    if (clearMode.includes("uploading")) {
      _abortUploading();
    }

    if (clearMode === "duplicates") {
      files.value = files.value.filter((f) => !f.duplicate);
      return;
    }

    files.value = files.value.filter((f) => !clearMode.includes(f.status));
  };

  watch(
    () => _options.value.api,
    (val) => (!!val ? $apiExcludeFromNotif(429, val) : null),
    { immediate: true },
  );

  onBeforeMount(() => {
    if (_options.value.dispose) {
      clear("all");
    }
  });

  return {
    files,
    uploading,
    paused,
    progress,
    addFile,
    removeFile,
    upload,
    fileBrowser,
    pause,
    resume,
    cancel,
    pauseAll,
    resumeAll,
    cancelAll,
    clear,
  };
};
