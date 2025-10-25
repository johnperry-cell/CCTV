import { isTimeMatches } from "@datasert/cronjs-matcher";

export default defineNuxtPlugin((nuxtApp) => {
  const { uniqid } = useUtils();

  type Task = {
    name: string;
    id: string;
    cb: any;
    cron: string;
  };
  const tasks = ref<Array<Task>>([]);

  const toPromise = <T extends (...args: any[]) => any>(
    fn: T,
  ): ((...args: Parameters<T>) => Promise<ReturnType<T> | void>) => {
    return (...args: Parameters<T>): Promise<ReturnType<T> | void> => {
      const result = fn(...args);

      // Check if the result is already a Promise
      if (result instanceof Promise) {
        return result; // If it's a promise, return it directly
      }

      // Otherwise, wrap the result in a promise
      return new Promise((resolve, reject) => {
        try {
          resolve(result); // Resolve with the result of the function
        } catch (error) {
          reject(error); // Reject if the function throws an error
        }
      });
    };
  };

  const addTask = (
    name: string,
    cron: string,
    cb: (...args: any[]) => any,
  ): string => {
    const id = uniqid("_cron_");
    tasks.value.push({ name, id, cb: toPromise(cb), cron });
    return id;
  };

  const removeTask = (id: string): void => {
    tasks.value = tasks.value.filter((t) => t.id !== id);
  };

  /**
   * Run scheduler every minute
   */
  const runScheduler = () => {
    const loop = () => {
      let now = new Date();
      const callbacks = tasks.value
        .filter((t) => isTimeMatches(t.cron, now.toISOString()))
        .map((t) => t.cb());
      Promise.all(callbacks);
      now = new Date(); // allow for time passing

      const delay = 60000 - now.getSeconds() * 1000; // exact ms to next minute interval
      setTimeout(loop, delay);
    };
    loop();
  };

  runScheduler();

  return { provide: { tasks, addTask, removeTask } };
});
