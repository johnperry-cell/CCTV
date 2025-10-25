import type { AxiosResponse } from "axios";
import type { HasKey, Prettify } from "~/types";

export type UploadOptionsSleep = Prettify<
  HasKey & {
    /**
     * Time in milliseconds to wait before continuing the upload.
     * Uploader sleeps for a short time between each chunk upload.
     *
     * @default 250
     * @type {number}
     */
    short: number;

    /**
     * Time in milliseconds to wait before continuing the upload.
     *
     * @default 1500
     * @type {number}
     */
    long: number;

    /**
     * Number of chunks to upload before long sleeping.
     *
     * @default 0 - disables long sleep
     * @type {number}
     */
    interval: number;
  }
>;

export type UploadOptionsIntegrity = Prettify<{
  /**
   * Check file integrity after upload.
   * Make sure that API endpoint returns file hash (SHA256) for integrity check to work.
   * May cause performance issues with large files.
   *
   * @default false
   * @type {boolean}
   */
  check?: boolean;

  /**
   * File hash key in API response.
   * Dot notation is supported.
   *
   * @default "sha256"
   * @type {string}
   */
  resultKey?: string;
}>;

export type UploadOptions = Prettify<
  HasKey & {
    /**
     * API endpoint for file upload. (Required)
     *
     * @type {string}
     */
    api: string;

    /**
     * Check file integrity after upload.
     * Make sure that API endpoint returns file hash (SHA256) for integrity check to work.
     *
     * @default undefined
     * @type {UploadOptionsIntegrity}
     */
    integrity?: UploadOptionsIntegrity;

    /**
     * Allow multiple file upload.
     *
     * @default false
     * @type {boolean}
     */
    multiple?: boolean;

    /**
     * Sleep options for controlling upload process.
     * This is used to throttle the upload process to avoid overloading the server.
     *
     * @type {UploadOptionsSleep}
     */
    sleep?: UploadOptionsSleep;

    /**
     * Rate in ms, how often to update upload speed, time remaining, and ellapsed time.
     *
     * @default 250
     * @type {number}
     */
    uprate?: number;

    /**
     * Upload concurrency limit.
     * Controls how many files can be uploaded at the same time.
     *
     * @default 1
     * @type {number}
     */
    concurrency?: number;

    /**
     * Maximum number of retries per chunk if it fails with error 429 (too many requests)
     *
     * @default 3
     * @type {number}
     */
    maxRetries?: number;

    /**
     * Dispose upload queue on before unmount.
     *
     * @default true
     * @type {boolean}
     */
    dispose?: boolean;

    /**
     * Upload events
     *
     * @type {UploadEvents}
     */
    events?: UploadEvents;
  }
>;

export type UploadEventResponse = {
  file: FileItem;
  response: AxiosResponse;
};

export type UploadEventError = {
  file: FileItem;
  error: any;
};

export interface UploadEvents {
  /**
   * Triggered when a file is uploaded
   *
   * @param file - {FileItem}
   * @returns
   */
  uploaded?: (result: UploadEventResponse) => void;

  /**
   * Triggered whan a file fails to upload
   *
   * @param file - {FileItem}
   * @returns
   */
  error?: (result: UploadEventError) => void;

  /**
   * Triggered when a file is cancelled
   *
   * @param file - {FileItem}
   * @returns
   */
  cancelled?: (files: Array<FileItem>) => void;

  /**
   * Triggered when a file is paused
   *
   * @param file - {FileItem}
   * @returns
   */
  paused?: (files: Array<FileItem>) => void;

  /**
   * Triggered when a file is resumed
   *
   * @param file - {FileItem}
   * @returns
   */
  resumed?: (files: Array<FileItem>) => void;
}

export type FileItemState = Prettify<
  HasKey & {
    /**
     * Unique identifier for the file
     *
     * @type {string}
     */
    uid?: string;

    /**
     * Current chunk to upload
     *
     * @type {number}
     */
    part: number;

    /**
     * Total number of chunks to upload
     *
     * @type {number}
     */
    parts: number;

    /**
     * Total number of bytes per chunk to upload
     *
     * @type {number}
     */
    length: number;

    /**
     * Percentage of upload complete
     * This progress is calculated by the api endpoint
     *
     * @type {number}
     */
    progress: number;
  }
>;

export type FileItemUpload = Prettify<
  HasKey & {
    /**
     * Timeout handler for updating upload speed
     *
     * @type {NodeJS.Timeout | null}
     */
    timer: NodeJS.Timeout | null;

    /**
     * Upload speed in bytes per second
     *
     * @type {number}
     */
    speed: number;

    /**
     * Timestamp when upload started
     *
     * @type {number}
     */
    start: number;

    /**
     * Time ellapsed since upload started
     *
     * @type {number}
     */
    ellapsed: number;

    /**
     * Number of bytes uploaded
     *
     * @type {number}
     */
    loaded: number;

    /**
     * Time remaining in ms
     *
     * @type {number}
     */
    timeRemaining: number;

    /**
     * Percentage of upload complete
     * This progress is calculated locally
     *
     * @type {number}
     */
    progress: number;

    /**
     * Pausing upload
     *
     * @type {boolean}
     */
    pausing: boolean;

    /**
     * Resuming upload
     *
     * @type {boolean}
     */
    resuming: boolean;

    /**
     * Canceling upload
     *
     * @type {boolean}
     */
    cancelling: boolean;

    /**
     * Abort controller for the current chunk upload
     *
     * @type {AbortController}
     */
    abortController?: AbortController;

    /**
     * Task promise for the current file upload
     *
     * @type {Promise<AxiosResponse>}
     */
    task?: {
      /**
       * Promise for the current file upload
       *
       * @type {Promise<AxiosResponse>}
       */
      promise: Promise<AxiosResponse>;

      /**
       * Called when the current file upload resolves
       *
       * @type {(value: AxiosResponse) => void}
       */
      resolve: (value: AxiosResponse) => void;

      /**
       * Called when the current file upload fails
       *
       * @type {(reason?: any) => void}
       */
      reject: (reason?: any) => void;
    };
  }
>;

export type FileStatus =
  | "pending"
  | "uploading"
  | "complete"
  | "error"
  | "paused"
  | "cancelled";

export type FileItem = Prettify<
  HasKey & {
    /**
     * Unique identifier for the file upload item
     *
     * @type {string}
     */
    id: string;

    /**
     * File to upload
     *
     * @type {File}
     */
    file: File;

    /**
     * File name
     *
     * @type {string}
     */
    name: string;

    /**
     * File status
     *
     * @type {FileStatus}
     */
    status: FileStatus;

    /**
     * File state
     *
     * @type {FileItemState}
     */
    state: FileItemState;

    /**
     * File upload
     *
     * @type {FileItemUpload}
     */
    upload: FileItemUpload;

    /**
     * Function to remove the file
     *
     * @param force - {boolean}
     * @returns
     */
    remove: (force: boolean) => void;

    /**
     * Pause the file upload
     *
     * @returns
     */
    pause: () => void;

    /**
     * Resume the file upload
     *
     * @returns
     */
    resume: () => void;

    /**
     * Cancel the file upload
     *
     * @returns
     */
    cancel: () => void;

    /**
     * Whether the file hash matches the hash from the server
     *
     * @type {boolean}
     */
    matchedHash?: boolean;

    /**
     * Whether the file hash is being calculated
     *
     * @type {boolean}
     */
    hashing: boolean;

    /**
     * Duplicate of the file
     *
     * @type {FileItem}
     */
    duplicate?: FileItem;
  }
>;
