export type Sidebar = {
  collapsed: boolean;
};

export const useSystemStore = defineStore("system", () => {
  const sidebar = ref<Sidebar>({
    collapsed: false,
  });

  return { sidebar };
});
