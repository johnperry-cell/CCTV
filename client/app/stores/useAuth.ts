import type { AxiosResponse } from "axios";
import type { Team } from "~/types/models/team";
import type {
  Profile,
  User,
  ProfileImage,
  UserRole,
  UserPermission,
} from "~/types/models/users";

export const useAuthStore = defineStore("auth", () => {
  const _super = import.meta.env.VITE_SUPERMAN ?? "Admin";

  const email = ref<string>();
  const username = ref<string>();
  const active = ref<boolean>(false);
  const verified = ref<string>();
  const roles = ref<Array<Omit<UserRole, "id">>>([]);
  const permissions = ref<Array<UserPermission | string>>([]);
  const images = ref<Array<ProfileImage>>([]);
  const team = ref<Team>();

  const profile = ref<Profile>();

  const isLoggedIn = computed(() => !!username.value);
  const hasTeam = computed(() => !!team.value);
  const canSwitchTeam = computed(
    () => hasTeam.value && roles.value.findIndex((r) => r.name == _super) > -1,
  );
  const hasProfileName = computed(() => !!profile.value?.full_name);

  const reset = () => {
    email.value = undefined;
    username.value = undefined;
    active.value = false;
    verified.value = undefined;
    roles.value = [];
    permissions.value = [];
    profile.value = undefined;
    team.value = undefined;
    clearCookies(["color.mode"]);
  };

  const setUser = (data: User) => {
    email.value = data.email;
    username.value = data.username;
    active.value = data.active;
    verified.value = data.verified;
    roles.value = (data.roles as Array<Omit<UserRole, "id">>) ?? [];
    permissions.value = data.permissions ?? [];
    profile.value = data.profile;
    images.value = data.profile?.images ?? [];
    team.value = data.team;
  };

  const login = async (payload: Object) => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .post("/auth/login", payload)
        .then((res) => {
          setUser(res.data.data);
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        });
    });
  };

  const switchTeam = (t: Team): Promise<AxiosResponse> => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .put("/auth/team", { team_id: t.id })
        .then((response) => {
          setUser(response.data.data);
          resolve(response);
        })
        .catch(reject);
    });
  };

  const handleHardReload = async (url: string) => {
    await fetch(url, {
      headers: {
        Pragma: "no-cache",
        Expires: "-1",
        "Cache-Control": "no-cache",
      },
    });
    window.location.href = url;
    // This is to ensure reload with url's having '#'
    window.location.reload();
  };

  const clearCookies = (exclude: Array<string> = []) => {
    const cookies = document.cookie.split(";");

    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i]?.trim() ?? "";
      const eqPos = cookie.indexOf("=");
      const name = eqPos > -1 ? cookie.slice(0, eqPos) : cookie;
      if (exclude.includes(name)) continue;

      // Deleting the cookie by setting an expired date
      document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=${location.hostname};`;

      // If cookies are set with 'Secure' or 'SameSite', handle it as well
      document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=${location.hostname}; Secure; SameSite=None;`;
    }
  };

  /**
   * Sign out user
   * @returns {Promise}
   */
  const logout = async (): Promise<any> => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .post("/auth/logout")
        .then((res) => {
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        })
        .finally(() => {
          reset();
          handleHardReload(window.location.href);
        });
    });
  };

  const getPermissions = async () => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .get("/auth/permissions")
        .then((res) => {
          setUser(res.data.data);
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        });
    });
  };

  return {
    email,
    username,
    verified,
    active,
    isLoggedIn,
    hasProfileName,
    roles,
    permissions,
    profile,
    canSwitchTeam,
    team,
    hasTeam,
    switchTeam,
    login,
    logout,
    getPermissions,
    setUser,
    reset,
  };
});
