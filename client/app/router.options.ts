import type { RouterConfig } from "@nuxt/schema";
import routes, { scrollBehavior } from "@/routes";

export default <RouterConfig>{
  scrollBehavior,
  routes: (_routes) => toValue(routes),
};
