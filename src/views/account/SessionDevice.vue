<template>
  <BOffcanvas
    v-model="backdropOff"
    :title="title"
    :backdrop="backdrop"
    header-class="border-bottom"
    body-class="p-0 overflow-hidden"
    footer-class="border-top p-3 text-center"
    placement="end"
  >
    <simplebar data-simplebar style="height: calc(100vh - 112px)">
      <div class="acitivity-timeline p-1">
        <div class="acitivity-item d-flex">
          <b-list-group>
            <BListGroupItem
              class="d-flex justify-content-between align-items-center"
            >
              ID Perangkat:
              <div class="bg-light bg-opacity-50 p-1">
                <b class="text-success">{{ deviceId }}</b>
              </div>
            </BListGroupItem>
          </b-list-group>
        </div>
      </div>
    </simplebar>
    <template #footer>
      <a href="javascript:void(0);" class="link-success"
        >View All Activity
        <i class="ri-arrow-right-s-line align-middle ms-1"></i
      ></a>
    </template>
  </BOffcanvas>
</template>
<script>
import simplebar from "simplebar-vue";

export default {
  components: {
    simplebar,
  },
  data() {
    return {
      backdrop: null,
      backdropOff: null,
      deviceInfo: null,
      title: `List Perangkat Aktif`,
      deviceId: null,
    };
  },
  methods: {
    async getListDevices() {},
  },
};
</script>
