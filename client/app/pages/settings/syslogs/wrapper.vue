<template>
  <TCard
    :ui="{
      root: 'max-h-full rounded-none border-none shadow-none',
      header: 'h-16 justify-start gap-2',
      body: 'flex flex-col gap-2 overflow-y-auto',
    }"
  >
    <template #header>
      <slot name="header" />
    </template>
    <slot />
    <template #footer>
      <slot name="footer" />
    </template>
  </TCard>
</template>
