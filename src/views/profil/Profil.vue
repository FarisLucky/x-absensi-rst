<template>
  <Layout>
    <PageHeader title="Profil" pageTitle="Jadwal" />
    <BRow class="justify-content-between">
      <BCol cols="12" md="6">
        <BCard no-body>
          <div class="id-card d-flex flex-column justify-content-start p-3">
            <div>
              <img
                v-if="karyawan?.photo !== null"
                :src="karyawan?.photo_url_cast"
                :key="new Date()"
                class="foto"
              />
              <img
                v-else
                src="@/assets/images/profil.jpg"
                :alt="karyawan?.nama + ' PT Catur Karsa Inkrisuba'"
                class="foto"
              />
            </div>
            <h4 style="color: #111827" class="mb-2">{{ karyawan?.nama }}</h4>
            <p class="mb-1">
              NIP Karyawan:
              <strong>{{ karyawan?.nip }}</strong>
            </p>
            <p class="mb-1">
              Jabatan:
              <strong>{{ karyawan?.jabatan }}</strong>
            </p>
            <p class="mb-1">
              Unit:
              <strong>{{ karyawan?.m_unit?.nama }}</strong>
            </p>
            <img src="@/assets/images/logo_new.png" width="40" class="logo" />
            <i
              class="ri-download-2-fill fs-18"
              style="
                position: absolute;
                right: 10px;
                bottom: 10px;
                cursor: pointer;
              "
              @click.prevent="downloadIdCard(karyawan?.nama)"
            ></i>
            <div
              class="d-flex justify-content-end"
              style="position: absolute; top: 74%"
            >
              <input
                ref="imgProfil"
                type="file"
                id="inputProfil"
                hidden
                @change="changeProfil"
                accept="image/png, image/gif, image/jpeg"
              />
              <button
                class="btn btn-sm btn-primary"
                @click="$refs.imgProfil.click()"
              >
                <i class="ri-edit-box-line align-bottom"></i>
                Edit Profile
              </button>
            </div>
          </div>
        </BCard>
      </BCol>
      <BCol>
        <BRow v-show="statistik?.list != undefined" class="mb-2 g-2">
          <BCol
            v-for="(row, idx) in statistik?.list"
            :md="6"
            class="col-6"
            :key="idx"
          >
            <ProfilGrafikMain :statistikData="row" />
          </BCol>
        </BRow>
      </BCol>
    </BRow>
    <ProfilTab />
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import { profileService } from "@/services/ProfileService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { defineAsyncComponent } from "vue";
import { toJpeg } from "html-to-image";
import imageBg from "@/assets/images/profil-bg2.png";

export default {
  components: {
    Layout,
    PageHeader,
    ProfilTab: defineAsyncComponent(() => import("./ProfilTab")),
    ProfilGrafikMain: defineAsyncComponent(() => import("./ProfilGrafikMain")),
  },
  data() {
    return {
      karyawan: {},
      statistik: {},
      imageBg,
    };
  },
  created() {
    this.onShow(this.$route.params.nip);
    this.getStatistik(this.$route.params.nip);
  },
  methods: {
    ...toastMethods,
    ...spinnerMethods,
    async onShow(nip) {
      this.show();
      const [err, resp] = await profileService.show(nip);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.karyawan = resp.data;
      console.log(this.karyawan);
      console.log(this.$route.params.nip);
      this.hide();
    },
    async getStatistik(nip) {
      const [err, resp] = await profileService.grafikStatistik(nip);
      if (err) {
        return;
      }
      this.statistik = resp.data;
    },
    async changeProfil() {
      if (this.$refs.imgProfil.files.length > 0) {
        this.show();

        let formData = new FormData();
        formData.append("file", this.$refs.imgProfil.files[0]);
        const [err, resp] = await profileService.changePhoto(formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        if (err) {
          this.toastError({
            title: "Gagal",
            msg: err.response?.data?.errors,
          });
          this.hide();

          return;
        }
        this.toastSuccess({
          title: "Berhasil",
          msg: resp.data,
        });
        this.$refs.imgProfil.value = "";
        this.hide();
        this.onShow();
      }
    },
    async downloadIdCard(name) {
      this.show();
      let dataUrl = await toJpeg(document.querySelector(".id-card"));

      var link = document.createElement("a");
      link.download = `idcard-${name}.jpeg`;
      link.href = dataUrl;
      link.click();
      this.hide();
    },
  },
};
</script>
<style scoped>
.id-card {
  height: 100%;
  border-radius: 10px;
  overflow: hidden;
  /* box-shadow: rgba(0, 0, 0, 0.15) 0px 3px 3px 0px; */
  background-size: auto 147%;
  color: #111827;
  position: relative;
  letter-spacing: 1px;
}
.id-card img.logo {
  border-radius: 50%;
  position: absolute;
  top: 10px;
  right: 20px;
}
.id-card img.foto {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  position: absolute;
  top: 30px;
  left: 25px;
  border: 2px solid white;
}
.id-card h4 {
  margin-top: 20px;
  margin-left: 120px;
}
.id-card p {
  margin-left: 120px;
  font-size: 13px;
  margin-bottom: 5px;
}
.id-card .badge {
  position: absolute;
  top: 20px;
  right: 20px;
}
</style>
