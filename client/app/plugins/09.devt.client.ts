import { addListener, launch } from "devtools-detector";

export default defineNuxtPlugin(() => {
  const devt = ref(false);

  const c = import.meta.env.VITE_DEVT;
  const isDev = import.meta.env.DEV;
  if (!(c === "true" || (c === "auto" && isDev))) {
    const redirectName = ref("403");
    const route = useRoute();
    const router = useRouter();

    const handle = () => {
      if (devt.value && route.name != redirectName.value) {
        const to = router.resolve({ name: redirectName.value }).href;
        window.open(to, "_self", "noopener=yes,noreferrer=yes")?.focus();
      }
    };

    watch([devt, route], handle);

    addListener((isOpen) => {
      devt.value = isOpen;
    });

    launch();
  }
  return { provide: { devt } };
});
