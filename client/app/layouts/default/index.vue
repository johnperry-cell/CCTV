<script setup lang="ts">
import type { BreadcrumbItem } from "#ui/types";
import menus from "./config/sidebar";
import aMenus from "./config/avatarOptions";
import SBActionButton from "@/components/sidebar/sb/actionButton.vue";

const $route = useRoute();
const $user = useAuthStore();

const sidebarExpand = ref(false);

const crumbs = computed<Array<BreadcrumbItem>>(() =>
  $route.matched
    .filter((r) => !!r.name)
    .map(
      (r): BreadcrumbItem => ({
        label: (r.meta.title as string) ?? r.name,
        to: r,
        icon: (r.meta.icon as string) ?? null,
      }),
    ),
);

const needsSidebar = computed<boolean>(
  () => !!$route.matched.find((r) => r.name === "settings"),
);
</script>

<template>
  <Layout>
    <Sidebar v-if="needsSidebar" v-model:expand="sidebarExpand" :menus>
      <template v-if="$user.isLoggedIn" #footer="{ expand }">
        <SBActionButton
          :expand
          :menu="{
            label: 'logout',
            icon: 'tabler:logout',
            action: () => $user.logout(),
          }"
          variant="solid"
          color="error"
          rounded
        >
        </SBActionButton>
      </template>
    </Sidebar>
    <LayoutBody
      class="transition-all"
      :class="{
        'ml-[4.5rem]': needsSidebar,
      }"
    >
      <TopNav fixed :avatarOptions="aMenus">
        <template v-if="!needsSidebar" #prepend>
          <TLink :to="{ name: 'home' }">
            <TAvatar src="/favicons/baguioseal.svg" size="2xl"> </TAvatar>
            {{ $config.public.short_name }}
          </TLink>
        </template>
        <template v-if="needsSidebar">
          <TButton
            color="neutral"
            variant="ghost"
            size="md"
            square
            icon="tabler:menu-2"
            @click="sidebarExpand = !sidebarExpand"
          />
          <TButton
            label="Home"
            icon="tabler:home"
            color="neutral"
            variant="ghost"
            :to="{ name: 'home' }"
          />
        </template>
        <div class="flex flex-auto items-center justify-end">
          <TTeamSelect />
        </div>
      </TopNav>
      <TPageTitle
        :title="($route.meta.title as string) ?? null"
        :icon="($route.meta.icon as string) ?? null"
        :breadcrumbs="crumbs"
      />
      <slot />
    </LayoutBody>
  </Layout>
</template>
