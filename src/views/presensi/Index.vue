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

<template>
    <Layout>
        <PageHeader title="Presensi" pageTitle="Presensi" />
        <router-view v-slot="{ Component, route }">
            <transition name="slide-fade" mode="out-in">
                <div :key="route.name">
                    <component :is="Component"></component>
                </div>
            </transition>
        </router-view>
    </Layout>
</template>
