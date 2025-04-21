<template>
  <div>
    <Vertical
      v-if="layoutType === 'vertical' || layoutType === 'semibox'"
      :layout="layoutType"
    >
      <transition name="slide-fade" appear mode="out-in">
        <div v-if="$slots.default">
          <slot />
        </div>
      </transition>
    </Vertical>
  </div>
</template>
<script>
import { layoutComputed } from "@/state/helpers";

import Vertical from "./vertical";

export default {
  components: {
    Vertical,
  },
  computed: {
    ...layoutComputed,
  },
};
</script>
