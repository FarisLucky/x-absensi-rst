<template>
  <BModal
    v-model="modal"
    id="ket"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    centered
    scrollable
    :title="title"
  >
    <div class="mb-1">
      <p class="fs-13 mb-1">Keterangan:</p>
      <div class="border rounded p-2">
        <strong class="fs-12">{{ ket }}</strong>
      </div>
    </div>
  </BModal>
</template>
<script>
export default {
  data() {
    return {
      modal: false,
      title: "",
      ket: "",
    };
  },
  methods: {
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
    },
  },
};
</script>
