export class UploadQueue {
  private queue: Array<() => Promise<any>> = [];
  private running = 0;
  private concurrency: number;

  constructor(concurrency: number = 1) {
    this.concurrency = concurrency;
  }

  add(task: () => Promise<any>) {
    return new Promise((resolve, reject) => {
      const wrapped = async () => {
        try {
          this.running++;
          const result = await task();
          resolve(result);
        } catch (err) {
          reject(err);
        } finally {
          this.running--;
          this._next();
        }
      };

      this.queue.push(wrapped);
      this._next();
    });
  }

  _next() {
    if (this.running >= this.concurrency || this.queue.length === 0) return;
    const nextTask = this.queue.shift();
    if (nextTask) nextTask();
  }

  setConcurrency(concurrency: number) {
    this.concurrency = concurrency;
  }

  clear() {
    this.queue = [];
    this.running = 0;
  }
}
