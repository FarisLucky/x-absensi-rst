<template>
  <BCard no-body>
    <BCardBody class="bg-cust">
      <KaryawanList />
    </BCardBody>
  </BCard>
</template>
<script>
import KaryawanList from "./KaryawanList.vue";

export default {
  components: {
    KaryawanList,
  },
};
</script>
