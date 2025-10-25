import type { Router } from "vue-router";
import { guard } from "@/routes";

export default defineNuxtPlugin((nuxtApp) => {
  const $router: Router = nuxtApp.$router as Router;
  $router.beforeEach(async (to, from, next) => {
    try {
      await guard(to, from);
      next();
    } catch (e: any) {
      next(e);
    }
  });
});
