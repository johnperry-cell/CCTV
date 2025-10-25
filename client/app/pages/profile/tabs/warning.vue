<template>
  <TAlert
    icon="tabler:alert-circle-filled"
    color="warning"
    variant="subtle"
    title="Account Restricted"
  >
    <template #title>
      <TTypography variant="h6" class="text-base font-semibold capitalize">
        Account is Limited
      </TTypography>
    </template>
    <template #description>
      <div class="space-y-3 text-sm">
        <p>
          This account has limited access and cannot modify certain profile
          information. The following actions are disabled:
        </p>
        <slot />
        <p>
          For any changes, please contact your
          <b>Account Administrator.</b>
        </p>
      </div>
    </template>
  </TAlert>
</template>
