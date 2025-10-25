export default defineNuxtPlugin(({ vueApp }) => {
  const $system = useSystemStore();

  watch($system.sidebar, (val) => {
    if (val.collapsed) {
      document.body.classList.add("overflow-clip");
    } else {
      document.body.classList.remove("overflow-clip");
    }
  });

  vueApp.provide("sidebar", $system.sidebar);
});
