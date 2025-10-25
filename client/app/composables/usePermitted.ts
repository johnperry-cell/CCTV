import type { RouteLocationNormalizedLoadedGeneric } from "vue-router";

const $router = useRouter();
const $guard = useGuard();

export const usePermitted = () => {
  const _permitted = (
    routeName: string,
    currentLocation?: RouteLocationNormalizedLoadedGeneric,
  ): boolean => {
    const perms: Array<string> | undefined = $router.resolve(
      Object.assign({}, { name: routeName }, currentLocation),
    )?.meta?.permissions as Array<string> | undefined;
    if (perms == null || perms == undefined) {
      return true;
    }
    return $guard.canAny(...perms);
  };

  /**
   * Checks if a route is permitted to be accessed by the current user based on their permissions
   *
   * @param routeName Any valid route names found in `routes/index.ts`
   * @param strictAll (optional) If true & the routeName is an array, all routes must be permitted
   * @param currentLocation (optional) Current route. By default, the currentLocation used is router.currentRoute and should only be overridden in advanced use cases.
   * @see https://router.vuejs.org/api/interfaces/RouteLocationNormalizedLoadedGeneric.html
   * @returns boolean
   */
  // prettier-ignore
  const isPermitted = (routeName: string | Array<string>, strictAll: boolean = false, currentLocation?: RouteLocationNormalizedLoadedGeneric): boolean => {
    if (Array.isArray(routeName)) {
      return routeName[strictAll ? "every" : "some"]((r: string) =>
        _permitted(r, currentLocation),
      );
    }
    return _permitted(routeName, currentLocation);
  };

  return { isPermitted };
};
