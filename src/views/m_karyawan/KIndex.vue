<template>
  <Layout>
    <PageHeader title="Karyawan" pageTitle="Pages" />
    <BCard no-body class="mb-2">
      <BCardBody>
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
      </BCardBody>
    </BCard>
    <router-view v-slot="{ Component, route }">
      <transition name="slide-fade" mode="out-in">
        <div :key="route.name">
          <component :is="Component"></component>
        </div>
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
  data() {
    return {
      list: [
        {
          title: "Data Karyawan",
          route: "KaryawanList",
          icon: "ri-file-user-line",
        },
        {
          title: "E-Dokumen",
          route: "KaryawanDok",
          icon: "ri-folders-line",
        },
        {
          title: "M-Jenis Dokumen",
          route: "KaryawanJenisDok",
          icon: "ri-file-list-line",
        },
      ],
      active: "KaryawanList",
    };
  },
};
</script>
