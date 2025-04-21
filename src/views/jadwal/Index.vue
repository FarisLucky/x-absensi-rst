<template>
  <Layout>
    <PageHeader :title="$route.meta.title" pageTitle="Jadwal" />
    <div class="h-100">
      <router-view v-slot="{ Component }" :key="$route.fullPath">
        <transition name="slide-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>
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
