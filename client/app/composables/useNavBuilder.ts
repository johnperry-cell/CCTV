import type { RouteLocationNormalizedLoadedGeneric } from "vue-router";
import type { MenuOption } from "~/types";

const $router = useRouter();
const $guard = useGuard();

export const useNavBuilder = () => {
  const resolveRoute = (
    routeName: string,
    currentLocation?: RouteLocationNormalizedLoadedGeneric,
  ) => {
    return $router.resolve(
      Object.assign({}, { name: routeName }, currentLocation),
    );
  };

  const getRouteMeta = (
    routeName: string,
    currentLocation?: RouteLocationNormalizedLoadedGeneric,
  ): any => {
    return resolveRoute(routeName, currentLocation)?.meta;
  };

  const getMenuItem = (
    routeName: string,
    currentLocation?: RouteLocationNormalizedLoadedGeneric,
  ): MenuOption => {
    const { isPermitted } = usePermitted();
    const meta = getRouteMeta(routeName, currentLocation);
    if (meta) {
      return Object.fromEntries(
        Object.entries({
          label: meta?.title ?? routeName,
          icon: meta.icon,
          to: { name: routeName },
          hidden: computed<boolean>(() => !isPermitted(routeName)),
          active: false,
          exact: false,
        }).filter(([_, v]) => v != null),
      );
    }
    return {};
  };

  const getNextValidChild = (routeName: string) => {
    const r = resolveRoute(routeName)?.matched[0].children ?? [];
    return r.find((c) => {
      const perms = c.meta?.permissions as Array<string> | undefined;
      return perms == undefined || $guard.canAny(...perms);
    });
  };

  const getMenuGroup = (
    routeName: string,
    children: Array<string>,
    navToParent: boolean = false,
    currentLocation?: RouteLocationNormalizedLoadedGeneric,
  ) => {
    let parent = getMenuItem(routeName);
    if (!navToParent) {
      parent.to = undefined;
    }
    if (parent) {
      parent.children = children.map((c) => getMenuItem(c, currentLocation));
    }
    return parent;
  };

  return { getMenuItem, getRouteMeta, getMenuGroup, getNextValidChild };
};
