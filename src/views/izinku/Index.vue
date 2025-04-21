<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";

export default {
  components: {
    Layout,
    PageHeader,
  },
  data() {
    return {
      list: [
        {
          title: "Progress",
          icon: "ri-restart-fill",
          route: "IzinkuProgress",
        },
        {
          title: "Konfirmasi",
          route: "IzinkuConfirm",
          icon: "ri-checkbox-multiple-fill",
        },
        {
          title: "Selesai",
          route: "IzinkuSelesai",
          icon: "ri-task-fill",
        },
      ],
    };
  },
};
</script>

<template>
  <Layout>
    <PageHeader title="Izin" pageTitle="Pages" />
    <BCard no-body class="mb-2">
      <BCardBody class="bg-cust">
        <div class="px-2 py-1">
          <p class="mb-2">Menu</p>
          <BNav pills>
            <BNavItem
              v-for="(tab, idx) in list"
              :key="idx"
              :active="$route.name === tab.route"
              @click.prevent="$router.push({ name: tab.route })"
            >
              <i :class="tab.icon" class="fs-14"></i>
              {{ tab.title }}
            </BNavItem>
          </BNav>
        </div>
      </BCardBody>
    </BCard>
    <BCard no-body class="mb-2">
      <BCardBody>
        <router-view v-slot="{ Component }" :key="$route.fullPath">
          <transition name="slide-fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </BCardBody>
    </BCard>
  </Layout>
</template>
