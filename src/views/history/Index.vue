<template>
    <Layout>
        <PageHeader :title="$route.meta.title" pageTitle="History" />
        <router-view v-slot="{ Component }" :key="$route.fullPath">
            <transition name="slide-fade" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>
    </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";

export default {
    components: {
        Layout,
        PageHeader,
    },
};
</script>
