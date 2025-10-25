<script setup lang="ts">
import aMenus from "../default/config/avatarOptions";
const $route = useRoute();
const $system = useSystemStore();
</script>

<template>
  <Layout>
    <LayoutBody>
      <TopNav fixed :avatarOptions="aMenus">
        <template #prepend>
          <TLink :to="{ name: 'home' }">
            <TAvatar src="/favicons/baguioseal.svg" size="2xl"> </TAvatar>
            {{ $config.public.short_name }}
          </TLink>
        </template>
      </TopNav>
      <slot />
    </LayoutBody>
  </Layout>
</template>
