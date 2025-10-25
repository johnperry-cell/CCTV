<script setup lang="ts"></script>

<template>
  <router-view v-slot="{ Component }">
    <transition
      enter-from-class="opacity-0 blur-md"
      leave-to-class="opacity-0 blur-md"
      enter-active-class="transition duration-300 delay-300"
      leave-active-class="transition duration-300"
    >
      <component :is="Component"> </component>
    </transition>
  </router-view>
</template>
