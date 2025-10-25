import type { Router } from "vue-router";

export default defineNuxtPlugin((nuxtApp) => {
  const $auth = useAuthStore();
  const $router: Router = nuxtApp.$router as Router;
  watch(
    () => $auth.isLoggedIn,
    (val) => {
      let to = {};
      if (!val) {
        to = {
          name: "login",
          query: { redirect: $router.currentRoute.value.fullPath },
        };
      } else {
        to = $router.currentRoute.value.query?.redirect || { name: "home" };
      }
      $router.push(to);
    },
  );
});
