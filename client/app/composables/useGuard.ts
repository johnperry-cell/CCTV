export const useGuard = () => {
  const $auth = useAuthStore();
  const _super = import.meta.env.VITE_SUPERMAN ?? "Admin";

  const isSuperAdmin = () => hasRole(_super);

  const can = (permission: string | string[]): boolean => {
    if (Array.isArray(permission)) {
      warn(
        "Guard function 'can(...)' will drop support for array parameters in future versions. Please use 'canAny' or 'canAll' instead.\nMultiple permissions delimited by '|' will be parsed as 'canAny(...)'.",
      );
      return can_old(permission);
    }

    const x = permission.split("|");
    if (x.length > 1) {
      return canAny(...x);
    }

    return hasPermission(permission) || isSuperAdmin();
  };

  const can_old = (permission: string | string[]): boolean => {
    if (Array.isArray(permission)) {
      return hasAnyPermission(permission) || isSuperAdmin();
    } else if (typeof permission === "string") {
      const x = permission.split("|");
      if (x.length > 1) {
        return can_old(x);
      }

      return hasPermission(permission) || isSuperAdmin();
    }
    return false;
  };

  const canAny = (...permissions: string[]): boolean => {
    return hasAnyPermission(parsePermissions(permissions)) || isSuperAdmin();
  };

  const canAll = (...permissions: string[]): boolean => {
    return hasAllPermissions(parsePermissions(permissions)) || isSuperAdmin();
  };

  const parsePermissions = (permissions: string[]): string[] => {
    let result: string[] = [];
    permissions.forEach((p) => {
      const x = p.split("|");
      if (x.length > 1) {
        result = result.concat(parsePermissions(x));
      } else {
        result.push(p);
      }
    });

    return [...new Set(result)];
  };

  const hasRole = (role: string): boolean => {
    if (
      $auth.roles.length > 0 &&
      $auth.roles.every((r) => typeof r === "string")
    ) {
      warn(
        'A new format has been introduced for auth roles. Please update your "UserResource"',
        $auth.roles,
      );
    }
    let roles = $auth.roles.map((r) => {
      if (r.name) return r.name;
      return r;
    });
    return !!role && roles.includes(role);
  };

  const hasAnyRole = (roles: string[]): boolean => {
    return roles.some((r) => hasRole(r));
  };

  const hasAllRoles = (roles: string[]): boolean => {
    return roles.length > 0 && roles.every((r) => hasRole(r));
  };

  const hasPermission = (permission: string): boolean => {
    return !!permission && $auth.permissions.includes(permission);
  };

  const hasAnyPermission = (permissions: string[]): boolean => {
    return permissions.some((p) => hasPermission(p));
  };

  const hasAllPermissions = (permissions: string[]): boolean => {
    return permissions.length > 0 && permissions.every((p) => hasPermission(p));
  };

  const hasAll = (permissions: string[], roles: string[]): boolean => {
    return hasAllPermissions(permissions) && hasAllRoles(roles);
  };

  const hasAny = (permissions: string[], roles: string[]): boolean => {
    return hasAnyPermission(permissions) || hasAnyRole(roles);
  };

  const hasNone = (permissions: string[], roles: string[]): boolean => {
    return !hasAnyPermission(permissions) && !hasAnyRole(roles);
  };

  return {
    isSuperAdmin,
    can,
    canAny,
    canAll,
    hasRole,
    hasAnyRole,
    hasAllRoles,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasAll,
    hasAny,
    hasNone,
  };
};
