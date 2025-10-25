export default defineNuxtPlugin(async () => {
  if (process.env.NODE_ENV == "development") {
    const tart = [
      " █████╗  ██████╗  █████╗ ██████╗ ",
      "██╔══██╗██╔════╝ ██╔══██╗██╔══██╗",
      "██║  ╚═╝██║  ██╗ ██║  ██║██████╦╝",
      "██║  ██╗██║  ╚██╗██║  ██║██╔══██╗",
      "╚█████╔╝╚██████╔╝╚█████╔╝██████╦╝",
      " ╚════╝  ╚═════╝  ╚════╝ ╚═════╝ ",
      "    City Government Of Baguio",
    ];
    console.log(tart.join("\n"));
  }

  const auth = useAuthStore();
  const { $router } = useNuxtApp();

  if (auth.isLoggedIn) {
    try {
      await auth.getPermissions();
    } catch (error: any) {
      if (error.response?.status === 429) {
        const retryAfter = error.response.headers["retry-after"];
        const delay = retryAfter ? parseInt(retryAfter, 10) * 1000 : 5000; // default to 5 seconds if retry-after header is not present
        let t = delay / 1000;

        const wrapper = document.createElement("div");
        const message1 = document.createElement("div");
        const message2 = document.createElement("div");
        wrapper.classList.add(
          "fixed",
          "inset-0",
          "flex",
          "flex-col",
          "items-center",
          "justify-between",
          "z-50",
          "text-center",
          "py-4",
        );
        wrapper.appendChild(message1);
        wrapper.appendChild(message2);
        document.body.appendChild(wrapper);

        const setMessageWaitTime = (time: number) => {
          message2.innerText = `The page will load automatically in ${time} seconds.`;
        };
        message1.innerText = `You've exceeded your request limit. Please try again later.`;
        setMessageWaitTime(t);

        const timer = setInterval(() => {
          setMessageWaitTime((t -= 1));
        }, 1000);

        await new Promise((resolve) =>
          setTimeout(() => {
            clearInterval(timer);
            message1.remove();
            message2.remove();
            wrapper.remove();

            // Reload the whole page (Comment this if you don't want to reload the whole page)
            window.location.reload();
            // Or continue without reloading...
            resolve(null);
          }, delay),
        );
        await auth.getPermissions(); // retry the request
      }
    }
  }

  watch(
    () => auth.isLoggedIn,
    (val) => {
      let to = {};
      if (!val) {
        to = {
          name: "login",
          query: { redirect: $router.currentRoute.value.fullPath },
        };
      } else {
        to = $router.currentRoute.value.query?.redirect || { name: "home" };
      }
      $router.push(to);
    },
  );
});
