import type {
  RouterScrollBehavior,
  RouteLocationNormalized,
  RouteLocationNormalizedLoaded,
} from "vue-router";
import { useGuard } from "~/composables/useGuard";

export const scrollBehavior: RouterScrollBehavior = (
  to,
  from,
  savedPosition,
) => {
  if (savedPosition) {
    return savedPosition;
  } else if (to.hash && !!document.querySelector(to.hash)) {
    let el = document.querySelector(to.hash);
    let y = (el?.getBoundingClientRect().top || 0) + window.scrollY - 120;
    return {
      top: y,
      behavior: "smooth",
    };
  } else {
    if (to.path != from.path) return { top: 0, left: 0, behavior: "smooth" };
  }
};

const setPageTitle = (
  to: RouteLocationNormalized,
  from?: RouteLocationNormalizedLoaded,
) => {
  const config = useRuntimeConfig();

  const nearestWithTitle = to.matched
    .slice()
    .reverse()
    .find((r) => r.meta && r.meta.title);

  const previousNearestWithMeta = from?.matched
    .slice()
    .reverse()
    .find((r) => r.meta && r.meta.metaTags);
  if (nearestWithTitle) {
    useHead({ title: nearestWithTitle.meta.title as string });
  } else if (previousNearestWithMeta) {
    useHead({ title: previousNearestWithMeta.meta.title as string });
  } else {
    useHead({ title: config.public.product_name as string });
  }
};

export const guard = async (
  to: RouteLocationNormalized,
  from?: RouteLocationNormalizedLoaded,
): Promise<RouteLocationNormalized> => {
  return new Promise((resolve, reject) => {
    const $auth = useAuthStore();
    const guard = useGuard();

    setPageTitle(to, from);

    const toRequiresVerified = to.matched.some(
      (record) => record.meta.requiresVerified,
    );

    if (to.meta.requiresAuth === true && !$auth.isLoggedIn) {
      reject({ name: "login", query: { redirect: to.fullPath } });
    } else if (to.meta.requiresAuth === false && $auth.isLoggedIn) {
      reject({ name: "home" }); // Redirect to the home page when signed in.
    } else if (toRequiresVerified && !$auth.verified) {
      reject({ name: "unverified", query: { redirect: to.fullPath } });
    } else if (
      $auth.isLoggedIn &&
      !$auth.hasProfileName &&
      to.name !== "update-profile"
    ) {
      reject({ name: "update-profile", query: { redirect: to.fullPath } });
    } else {
      if (!!to.meta.permissions) {
        if (!guard.canAny(...(to.meta.permissions as string[]))) {
          if (!!to.meta.redirect) {
            reject(to.meta.redirect);
          } else {
            reject({ name: "401", query: { redirect: to.fullPath } });
          }
          return; // Ensure the function exits after the rejection.
        }
      }
    }
    resolve(to);
  });
};
