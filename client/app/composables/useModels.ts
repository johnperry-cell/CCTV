import type { HasKey } from "~/types";

export const useModels = () => {
  // prettier-ignore
  const merge = <T extends HasKey>(models: Array<T>, updated: T, addIfNotExists: boolean = true, key: keyof T = "id"): void=> {
    const index = models.findIndex((m: T) => m[key] == updated[key]);
   
    if (index === -1 && addIfNotExists) {
      models.unshift(updated);
    } else if (index !== -1) {
      models[index] = { ...models[index], ...updated };
    }
  };

  return {
    merge,
  };
};
