<style scoped>
.vgt-table.polar-bear tr td:first-child() {
  position: sticky;
  left: 0;
}
</style>
<template>
  <BModal
    v-model="modal"
    id="lihatJadwal"
    modal-class="zoomIn"
    hide-footer
    header-class="p-3 bg-info-subtle"
    class="v-modal-custom"
    title="Acc Oleh"
  >
    <div class="p-2">
      <label class="form-label">ACC Oleh</label>
      <textarea class="form-control" rows="2" :value="accBy" disabled>
      </textarea>
    </div>
  </BModal>
</template>
<script>
export default {
  data() {
    return {
      modal: false,
      accBy: "",
      title: "",
    };
  },
  methods: {
    showModal() {
      this.modal = true;
    },
    hideModal() {
      this.modal = false;
      this.reset();
    },
  },
};
</script>
