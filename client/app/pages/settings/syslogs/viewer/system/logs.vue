<script setup lang="ts">
import { useVirtualList } from "@vueuse/core";
import LogItem from "./logItem.vue";

const props = defineProps<{
  log: Array<string>;
  wrap: boolean;
}>();

const logs = computed(() => props.log);
const longest = ref(0);

const { list, containerProps, wrapperProps } = useVirtualList(logs, {
  itemHeight: 26,
});

const getLongest = (l: Array<string>): number => {
  let result = 0;
  l.forEach((ll) => {
    if (ll.length > result) {
      result = ll.length;
    }
  });
  return result;
};

onMounted(() => {
  longest.value = getLongest(props.log);
});
</script>

<template>
  <div v-bind="containerProps" style="height: 100%">
    <div v-bind="wrapperProps" class="[]" style="width: max-content">
      <LogItem
        v-for="item in list"
        :key="item.index"
        :lnWidth="7.7 * (logs.length + '').length + 4"
        :ln="item.index + 1"
        :length="longest"
        :noWrap="wrap"
        style="height: 26px"
      >
        {{ item.data }}
      </LogItem>
    </div>
  </div>
</template>
