<template>
  <Layout>
    <PageHeader title="Jadwal" pageTitle="Harian" />
    <PresensiBulanan />
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import PresensiBulanan from "@/views/harian/jadwal/PresensiBulanan";

export default {
  components: {
    Layout,
    PageHeader,
    PresensiBulanan,
  },
};
</script>
