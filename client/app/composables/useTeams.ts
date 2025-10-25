export const useTeams = (cb: <T>(...args: T[]) => void) => {
  const user = useAuthStore();
  watch(() => user.team, cb);
};
