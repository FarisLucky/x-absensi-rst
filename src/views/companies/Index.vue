<template>
  <Layout>
    <PageHeader title="Perusahaan" pageTitle="Pengaturan" />
    <div class="h-100">
      <BRow>
        <BCol xl="12">
          <BCard no-body>
            <BCardHeader class="border-0">
              <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Profil Perusahaan</h5>
              </div>
            </BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <BForm @submit.prevent="onSubmit">
                <BRow class="g-2">
                  <BCol cols="6">
                    <img
                      v-if="form.logo !== '' && form.logo !== null"
                      :src="form.logo_url_cast"
                      width="120"
                      height="120"
                      alt="Logo PT"
                    />
                    <img
                      v-else
                      :src="logoPT"
                      alt="Logo PT"
                      width="120"
                      height="120"
                    />
                  </BCol>
                  <BCol cols="6">
                    <div clas="d-flex g-2" style="flex-direction: column">
                      <div>
                        <label>Nama</label>
                        <input
                          type="text"
                          class="form-control"
                          v-model="form.nama"
                        />
                      </div>
                      <div>
                        <label>Short</label>
                        <input
                          type="text"
                          class="form-control"
                          v-model="form.short"
                        />
                      </div>
                    </div>
                    <div class="mb-1">
                      <label>Alamat</label>
                      <input
                        type="text"
                        class="form-control"
                        v-model="form.alamat"
                      />
                    </div>
                  </BCol>
                  <BCol cols="12" md="6">
                    <label>Telp</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="form.telp"
                    />
                  </BCol>
                  <BCol cols="12" md="6">
                    <label>Email</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="form.email"
                    />
                  </BCol>
                  <BCol cols="12" class="text-end">
                    <BButton type="submit" variant="primary">Simpan</BButton>
                  </BCol>
                </BRow>
              </BForm>
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
    </div>
  </Layout>
</template>
<script>
import Layout from "@/layouts/main.vue";
import PageHeader from "@/components/page-header";
import { toastMethods } from "@/state/helpers";
import { companiesService } from "@/services/CompaniesService";
import logoPT from "@/assets/images/logopt.jpg";

const initForm = () => ({
  id: "",
  nama: "",
  short: "",
  logo: "",
  logo_url_cast: "",
  alamat: "",
  telp: "",
  email: "",
});

export default {
  components: {
    Layout,
    PageHeader,
  },
  data() {
    return {
      form: initForm(),
      logoPT,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...toastMethods,
    async fetchData() {
      this.isLoading = true;

      const [err, resp] = await companiesService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }
      this.rows = resp.data;
      this.isLoading = false;
    },
    async onSubmit() {
      this.isLoading = true;

      const [err] = await companiesService.update(this.form, this.form.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.isLoading = false;

        return;
      }

      this.toastSuccess({
        title: "Berhasil",
        msg: "Data Berhasil disimpan",
      });
    },
  },
};
</script>
