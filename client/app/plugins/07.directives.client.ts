import Directives from "~/directives";

export default defineNuxtPlugin(({ vueApp }) => {
  for (let d in Directives) {
    Directives[d as keyof typeof Directives](vueApp);
  }
});
