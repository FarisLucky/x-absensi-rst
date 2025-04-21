<template>
  <div>
    <BRow class="g-1">
      <BCol
        :lg="12"
        style="text-wrap: wrap; overflow-wrap: break-word; overflow: hidden"
      >
        <div class="d-flex align-items-center mb-3 mt-1">
          <div class="flex-shrink-0 avatar-sm me-2">
            <img
              v-if="izin?.pemohon?.photo !== null"
              :src="izin?.pemohon?.photo_url_cast"
              class="img-fluid d-block rounded"
              style="width: 60px"
            />
            <img
              v-else
              src="@/assets/images/profil.jpg"
              class="img-fluid d-block rounded"
              style="width: 60px"
            />
          </div>
          <div class="flex-shrink-0 ms-2">
            <div class="mb-2">
              <h6 class="fs-14 mb-1">{{ izin.nama }} ({{ izin.nip }})</h6>
              <span class="badge badge-gradient-primary">{{
                izin?.pemohon?.unit
              }}</span>
            </div>
          </div>
        </div>
      </BCol>
      <BCol md="12">
        <h5 class="fs-13">
          Menunggu Persetujuan:
          <BLink
            variant="danger"
            size="sm"
            @click.prevent="viewHistoryIzin(izin)"
            class="ms-1"
          >
            <i class="ri-information-line fs-12"></i>
            Lihat History izin
          </BLink>
        </h5>
        <timeline :izin="izin" />
      </BCol>
      <BCol md="12">
        <div class="accordion accordion-flush rounded px-0">
          <div class="accordion-item border-0">
            <div class="accordion-header">
              <a
                class="accordion-button px-0 collapsed shadow-none bg-white"
                data-bs-toggle="collapse"
                :href="`#${izin.id}_detail`"
                aria-expanded="true"
              >
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="fs-14 mb-0">Detail Izin:</h6>
                  </div>
                </div>
              </a>
            </div>
            <div
              :id="izin.id + '_detail'"
              class="accordion-collapse collapse show"
            >
              <div class="accordion-body m-0 p-2 fs-12 text-dark">
                <BRow class="g-2">
                  <BCol md="2" cols="6">
                    <h6 class="mb-1">Pengajuan:</h6>
                    <strong>
                      {{ izin.created_at_cast }}
                    </strong>
                  </BCol>
                  <BCol md="2" cols="6">
                    <h6 class="mb-1">Kode Izin:</h6>
                    <div class="mb-1">
                      <span>{{ izin?.kode_izin }}</span>
                    </div>
                  </BCol>
                  <BCol md="3" cols="6">
                    <div class="mb-1">
                      <h6 class="mb-1">Mulai / Akhir / Masuk:</h6>
                      <div class="mb-1">
                        <span>
                          {{
                            `${izin?.mulai_cast} / ${izin?.akhir_cast} / ${izin?.masuk_cast}`
                          }}
                        </span>
                      </div>
                    </div>
                  </BCol>
                  <BCol md="2" cols="6">
                    <h6 class="mb-1">Periode:</h6>
                    <span>
                      {{ `${izin?.periode} Hari` }}
                    </span>
                  </BCol>
                  <BCol md="2" cols="6">
                    <h6 class="mb-1">Diambil / Sisa:</h6>
                    <span>
                      {{ `${izin?.cuti_diambil} / ${izin.sisa} Hari` }}
                    </span>
                  </BCol>
                  <BCol md="4" cols="6">
                    <h6 class="mb-1">Keterangan:</h6>
                    <div class="mb-1">
                      <span v-if="izin.ket !== null">{{ izin.ket }}</span>
                      <span v-else>-</span>
                    </div>
                  </BCol>
                  <BCol v-if="izin?.izin_bukti !== null" md="2" cols="6">
                    <h6 class="mb-1">Bukti:</h6>
                    <div class="mb-1">
                      <button
                        class="btn btn-soft-success waves-effect waves-light"
                        @click.prevent="show"
                      >
                        <i class="ri-image-2-fill"></i>
                        Lihat
                      </button>
                      <div class="images d-none" v-viewer="{ movable: false }">
                        <img v-for="src in bukti" :key="src" :src="src" />
                      </div>
                    </div>
                  </BCol>
                </BRow>
              </div>
            </div>
          </div>
        </div>
      </BCol>
    </BRow>
    <ListHistoryIzin ref="listHistoryIzinRef" />
  </div>
</template>
<script>
import { IZIN_CUTI, IZIN_KRS, ICT } from "@/helpers/utils";
import "viewerjs/dist/viewer.css";
import { directive as viewer } from "v-viewer";
import { webUrl } from "@/config/http";
import Timeline from "./Timeline";
import { defineAsyncComponent } from "vue";

export default {
  components: {
    Timeline,
    ListHistoryIzin: defineAsyncComponent(() =>
      import("./ListHistoryIzin.vue")
    ),
  },
  props: ["izin", "id"],
  data() {
    return { IZIN_CUTI, IZIN_KRS, ICT, bukti: [] };
  },
  directives: {
    viewer: viewer({
      debug: false,
    }),
  },
  created() {
    this.getBukti(this.id);
  },
  methods: {
    getBukti(id) {
      let file = `${webUrl}/izin/bukti/${id}`;

      this.bukti.push(file);
    },
    show() {
      const viewer = this.$el.querySelector(".images").$viewer;
      viewer.show();
    },
    viewHistoryIzin(izin) {
      this.$refs.listHistoryIzinRef.show = true;
      this.$refs.listHistoryIzinRef.nip = izin.nip;
    },
  },
};
</script>
<style scoped>
.list-group-item {
  border: none;
  padding: 0.8rem;
}
</style>
