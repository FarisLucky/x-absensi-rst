<template>
  <Layout>
    <PageHeader title="Harian Kerja" pageTitle="Harian" />
    <div class="h-100">
      <HarianKerja />
    </div>
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import HarianKerja from "./HarianKerja.vue";

export default {
  components: {
    Layout,
    PageHeader,
    HarianKerja,
  },
};
</script>
