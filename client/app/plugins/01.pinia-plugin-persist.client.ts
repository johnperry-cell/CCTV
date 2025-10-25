import { createPersistedStatePlugin } from "pinia-plugin-persistedstate-2";
import { EncryptStorage } from "encrypt-storage";
import type { Pinia } from "pinia";
import { PiniaSharedState } from "pinia-shared-state";

export default defineNuxtPlugin(({ $pinia }) => {
  const storeEncrypt = import.meta.env.VITE_STORE_ENCRYPT;
  const storeSalt = import.meta.env.VITE_STORE_SALT;
  const storePrefix = import.meta.env.VITE_STORE_PREFIX;

  const encrypt =
    storeEncrypt === "true" ||
    (storeEncrypt === "auto" && import.meta.env.PROD);

  const pinia: Pinia = $pinia as Pinia;
  let persistOptions = {};

  if (encrypt && !!pinia) {
    const ls = new EncryptStorage(storeSalt, {
      prefix: storePrefix,
      stateManagementUse: true,
    });

    persistOptions = {
      storage: {
        getItem: (key: string) => {
          let data = ls.getItem(ls.hash(key))?.replace(/^\s+|\s+$/gm, "") ?? "";
          if (data == "") return "[]";
          return data;
        },
        setItem: (key: string, value: any) => ls.setItem(ls.hash(key), value),
        removeItem: (key: any) => ls.removeItem(ls.hash(key)),
      },
    };
  }
  pinia.use(createPersistedStatePlugin(persistOptions));
  pinia.use(PiniaSharedState({ type: "localstorage" }));
});
