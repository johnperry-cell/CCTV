<template>
  <Layout>
    <div class="fixed inset-0 -z-10">
      <div class="bg-dots h-full w-full"></div>
    </div>
    <LayoutBody class="">
      <div
        class="bg-default/5 border-default min-h-12 gap-1.5 border-b px-6 py-3 backdrop-blur-sm"
      >
        <TLink :to="{ name: 'home' }">
          <TAvatar src="/favicons/baguioseal.svg" size="2xl"> </TAvatar>
          {{ $config.public.short_name }}
        </TLink>
      </div>
      <div class="flex h-full items-center justify-center">
        <slot />
      </div>
    </LayoutBody>
  </Layout>
</template>
