<script setup lang="ts">
const { $pwa } = useNuxtApp();

const toast = useToast();

onMounted(() => {
  if ($pwa?.offlineReady) {
    toast.add({
      title: "Offline Ready",
      description: "App ready to work offline",
      icon: "tabler:wifi-off",
    });
  }
});
</script>

<template>
  <Teleport to="#teleports">
    <Transition
      enter-from-class="translate-x-full opacity-0 blur-md"
      leave-to-class="translate-x-full opacity-0 blur-md"
      enter-active-class="transition duration-300 delay-300"
      leave-active-class="transition duration-300"
    >
      <div
        v-show="$pwa?.needRefresh"
        class="max-w-screen-95 fixed bottom-24 right-4 z-[999] flex flex-col gap-2 rounded-lg border border-gray-400/25 bg-gray-100 px-4 py-2 shadow-md dark:bg-gray-700 dark:shadow-gray-100/15"
      >
        <TTypography>
          New content available, click on reload button to update.
        </TTypography>
        <div class="flex items-center gap-2 *:min-w-16">
          <TButton
            label="Reload"
            class="justify-center"
            @click="$pwa?.updateServiceWorker()"
          />
          <TButton
            label="Close"
            color="neutral"
            variant="ghost"
            class="justify-center"
            @click="$pwa?.cancelPrompt()"
          />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
