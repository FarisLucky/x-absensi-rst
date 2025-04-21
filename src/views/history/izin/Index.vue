<template>
    <router-view v-slot="{ Component }" :key="$route.fullPath">
        <transition name="fade" mode="out-in">
            <component :is="Component" />
        </transition>
    </router-view>
</template>
<script>
import List from "./List";

export default {
    components: {
        List,
    },
};
</script>
