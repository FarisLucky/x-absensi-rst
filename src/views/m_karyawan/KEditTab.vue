<template>
  <div>
    <!-- <BCard no-body class="p-0 mb-2">
      <BCardBody>
        <div class="px-2 py-1">
          <p class="mb-2">Menu</p>
          <BNav pills>
            <BNavItem active>Data Diri</BNavItem>
          </BNav>
        </div>
      </BCardBody>
    </BCard> -->
    <BCard>
      <BCardHeader>
        <h5 class="fs-14">Edit Data Karyawan</h5>
      </BCardHeader>
      <BCardBody>
        <KEdit />
      </BCardBody>
    </BCard>
  </div>
</template>
<script>
import { defineAsyncComponent } from "vue";

export default {
  components: {
    KEdit: defineAsyncComponent(() => import("@/views/m_karyawan/KEdit")),
  },
  //   data() {
  //     return {
  //       list: [
  //         {
  //           title: "Data Diri",
  //           component: "KEdit",
  //           route: "datadiri",
  //           icon: "ri-shield-user-line",
  //         },
  //       ],
  //       active: "ProfilPribadi",
  //     };
  //   },
};
</script>
