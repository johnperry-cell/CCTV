import type { AvatarOptions } from "~/types";

const { getMenuItem, getNextValidChild } = useNavBuilder();

export default computed<Array<AvatarOptions>>(() => [
  getMenuItem("profile"),
  { label: "Help", icon: "tabler:help" },
  { divider: false },
  Object.assign(getMenuItem("settings"), {
    to: { name: getNextValidChild("settings")?.name },
  }),
]);
