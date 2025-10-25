import type { MenuOption } from "~/types";

const $route = useRoute();
const { getMenuItem, getMenuGroup } = useNavBuilder();

/**
 * Commented items are from "SC3 Dashboard" and is left here for reference.
 */
export const home = shallowRef([
  // getMenuItem("environment"),
  // getMenuItem("gis"),
  // getMenuItem("sensors"),
  // { divider: true },
  // getMenuGroup("surveillance-system", ["ss-video-wall", "ss-anpr-data"]),
  // getMenuItem("resources"),
  // getMenuGroup("health", ["dengue"]),
  // getMenuItem("mortality"),
  // getMenuItem("law"),
]);

export const settings = shallowRef([
  // getMenuItem("settings-air-sensors"),
  // getMenuItem("settings-philsensors"),
  // getMenuItem("settings-sensors"),
  // getMenuItem("settings-gis"),
  { divider: true },
  getMenuItem("settings-accounts"),
  getMenuItem("settings-roles"),
  getMenuItem("settings-permissions"),
  getMenuItem("settings-logs"),
  getMenuItem("settings-clients"),
]);

export default computed<Array<MenuOption>>(() => {
  const layoutName = $route.matched[0]?.name as string;
  if (layoutName === "settings") return settings.value;
  if (layoutName === "home") return home.value;
  return [];
});
