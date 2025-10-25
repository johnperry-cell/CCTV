<script setup lang="ts">
const { $pwa } = useNuxtApp();

const open = ref(false);
</script>

<template>
  <TPopover v-if="!!$pwa && $pwa.showInstallPrompt" v-model:open="open">
    <TButton
      color="neutral"
      variant="ghost"
      size="md"
      square
      icon="tabler:device-mobile-down"
      :ui="{ leadingIcon: 'size-6' }"
    />
    <template #content>
      <div class="max-w-screen-95 flex flex-col gap-2 px-3 py-2">
        <div class="flex items-center gap-2">
          <TIcon name="tabler:device-mobile-down" class="h-10 w-10" />
          <div class="flex flex-col">
            <TTypography variant="sm">Install Web App?</TTypography>
            <TTypography variant="xs">
              This site can be installed as an application.
            </TTypography>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 *:min-w-16">
          <TButton label="Install" @click="$pwa?.install()" />
          <TButton
            label="Cancel"
            variant="ghost"
            color="neutral"
            @click="open = false"
          />
        </div>
      </div>
    </template>
  </TPopover>
</template>
