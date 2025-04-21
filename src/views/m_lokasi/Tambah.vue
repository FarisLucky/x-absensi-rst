<template>
  <div>
    <BForm id="addform" class="tablelist-form" autocomplete="off">
      <p class="text-end m-0 mb-1">
        (<strong class="text-danger">*</strong>) tidak boleh kosong
      </p>
      <div class="p-2">
        <h5 class="fs-14 mb-2 d-inline-block border-bottom pb-1">
          Data Lokasi
        </h5>
        <BRow class="g-2 mb-1">
          <BCol lg="3">
            <label for="nama" class="form-label">
              Nama
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="nama"
              class="form-control"
              placeholder="Lokasi 1..."
              v-model="form.nama"
            />
          </BCol>
          <BCol lg="3">
            <label for="latitude" class="form-label">
              Latitude
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="latitude"
              class="form-control"
              placeholder="3.2378783..."
              v-model="form.latitude"
            />
          </BCol>
          <BCol lg="3">
            <label for="longitude" class="form-label">
              Longitude
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              id="longitude"
              class="form-control"
              placeholder="-3.2378783..."
              v-model="form.longitude"
            />
          </BCol>
          <BCol lg="1">
            <label for="radius" class="form-label">
              Radius
              <span class="text-danger">* (m)</span>
            </label>
            <input
              type="number"
              id="radius"
              class="form-control"
              placeholder="3..."
              v-model="form.radius"
            />
          </BCol>
          <BCol lg="2">
            <label for="status" class="form-label">
              Status
              <span class="text-danger">*</span>
            </label>
            <v-select
              v-model="form.status"
              :options="[
                {
                  id: 1,
                  val: 'AKTIF',
                },
                {
                  id: 0,
                  val: 'NONAKTIF',
                },
              ]"
              :reduce="(st) => st.id"
              label="val"
              placeholder="Pilih Status Aktif"
            ></v-select>
          </BCol>
          <BCol :lg="12" class="justify-self-end">
            <div class="mb-1 text-end">
              <BButton
                type="submit"
                :variant="dataEdit ? 'info' : 'primary'"
                @click.prevent="onSubmit"
                class="me-1"
              >
                <i class="ri-save-2-line me-1 align-bottom"></i>
                {{ dataEdit ? "Ubah" : "Tambah" }}
              </BButton>
              <BButton
                type="reset"
                variant="outline-secondary"
                @click="resetForm"
              >
                <i class="ri-refresh-fill me-1 align-bottom"></i>
                Reset
              </BButton>
            </div>
          </BCol>
        </BRow>
      </div>
      <div class="bg-layer text-center">
        <div
          v-if="showMap"
          style="height: 70vh; width: 100%; margin-bottom: 0.7rem"
        >
          <l-map
            :zoom="zoom"
            :center="[form.latitude, form.longitude]"
            :fade-animation="true"
            :marker-zoom-animation="true"
            ref="mapLeaflet"
            @ready="mapReady"
          >
            <l-tile-layer
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            ></l-tile-layer>
            <l-control-layers />
            <l-marker :lat-lng="[form.latitude, form.longitude]">
              <l-tooltip> koordinat absen </l-tooltip>
            </l-marker>
            <l-circle
              :lat-lng="[form.latitude, form.longitude]"
              :radius="form.radius"
              :fill="true"
              color="#F06A4E"
            >
            </l-circle>
          </l-map>
        </div>
      </div>
    </BForm>
  </div>
</template>
<script>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { mLokasiService } from "@/services/MLokasiService";
import { toastMethods, toastState } from "@/state/helpers";
import "leaflet/dist/leaflet.css";
import {
  LMap,
  LTileLayer,
  LMarker,
  LControlLayers,
  LTooltip,
  LCircle,
} from "@vue-leaflet/vue-leaflet";

const initForm = () => ({
  id: "",
  nama: "",
  latitude: "-7.726779",
  longitude: "113.479253",
  radius: 30,
  status: "",
});

export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LControlLayers,
    LTooltip,
    LCircle,
  },
  setup() {
    return { v$: useVuelidate() };
  },
  data() {
    return {
      form: initForm(),
      submitted: false,
      timeConfig: {
        enableTime: false,
        dateFormat: "d M, Y",
      },
      dataEdit: false,
      zoom: 16,
      marker: null,
      showMap: false,
    };
  },
  watch: {
    "form.nama"(newValue) {
      this.form.nama = newValue.toString().toUpperCase();
    },
  },
  validations() {
    return {
      form: {
        nama: { required },
        longitude: { required },
        latitude: { required },
        radius: { required },
        status: { required },
      },
    };
  },
  computed: {
    ...toastState,
  },
  created() {
    this.resetForm();
  },
  methods: {
    ...toastMethods,

    async onUpdate() {
      const [err] = await mLokasiService.update(this.form, this.form.id);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.$emit("fetch");
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil diubah",
      });
      this.resetForm();
      this.$emit("close");
    },

    async onStore() {
      const [err] = await mLokasiService.store(this.form);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        return;
      }
      this.$emit("fetch");
      this.toastSuccess({
        title: "Berhasil",
        msg: "Berhasil ditambahkan",
      });
      this.resetForm();
      this.$emit("close");
    },

    async onSubmit() {
      const result = await this.v$.$validate();
      console.log(result);
      if (!result) {
        this.toastError({
          title: "Gagal",
          msg: "Form wajib diisi",
        });
        return;
      }
      if (this.dataEdit) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },

    setUpdateData(params, editable) {
      this.dataEdit = editable;
      this.form.id = params?.id;
      this.form.nama = params?.nama;
      this.form.latitude = params?.latitude;
      this.form.longitude = params?.longitude;
      this.form.radius = params?.radius;
      this.form.status = parseInt(params?.status);
    },
    mapReady(map) {
      map.on("click", (e) => {
        this.form.latitude = e.latlng.lat;
        this.form.longitude = e.latlng.lng;
        console.log(e);
      });
    },
    toggleMap() {
      this.showMap = !this.showMap;
    },
    resetForm() {
      this.form = initForm();
    },
  },
};
</script>
