<template>
    <div class="p-2">
        <BRow class="g-2 align-items-end">
            <BCol cols="12">
                <span>Filter Berdasarkan</span>
            </BCol>
            <BCol lg="3">
                <label class="mb-1">Range Bulan</label>
                <flat-pickr
                    v-model="filter.range"
                    placeholder="Pilih Range Bulan"
                    :config="flatConfig"
                    class="form-control bg-light border-light"
                    required
                ></flat-pickr>
            </BCol>
            <BCol md="4">
                <label class="mb-1">Unit</label>
                <v-select
                    v-model="filter.unit"
                    :options="unitList"
                    :reduce="(unit) => unit.id"
                    label="nama"
                    placeholder="Pilih Unit"
                    @update:modelValue="onCheckMultiple"
                    multiple
                ></v-select>
            </BCol>
            <div class="col-lg">
                <BButton variant="outline-success" @click.prevent="getUrl">
                    <i class="ri-file-excel-2-fill me-1 align-bottom"></i>
                    Export
                </BButton>
            </div>
        </BRow>
    </div>
</template>
<script>
import { webUrl } from "@/config/http";
import { mUnitService } from "@/services/MUnitService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import queryString from "query-string";
import flatPickr from "vue-flatpickr-component";
import monthSelect from "flatpickr/dist/plugins/monthSelect";
import "flatpickr/dist/plugins/monthSelect/style.css";

export default {
    components: {
        flatPickr,
    },
    data() {
        return {
            filter: {
                range: "",
                unit: null,
            },
            webUrl,
            unitList: [],
            flatConfig: {
                mode: "range",
                plugins: [
                    new monthSelect({
                        shorthand: true, //defaults to false
                        dateFormat: "Y-m", //defaults to "F Y"
                        altFormat: "F Y", //defaults to "F Y"
                    }),
                ],
            },
        };
    },
    created() {
        this.getUnit();
    },
    methods: {
        ...spinnerMethods,
        ...toastMethods,
        async getUnit() {
            this.show();
            const [err, resp] = await mUnitService.all();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.hide();

                return;
            }
            this.unitList = resp.data;
            this.unitList.unshift({
                id: -1,
                nama: "SEMUA",
            });
            this.hide();
        },
        getUrl() {
            if (this.filter.mulai === "") {
                this.toastError({
                    title: "Gagal",
                    msg: "Tanggal Mulai tidak boleh kosong",
                });
                return;
            } else if (this.filter.selesai === "") {
                this.toastError({
                    title: "Gagal",
                    msg: "Tanggal Selesai tidak boleh kosong",
                });
                return;
            }

            let a = document.createElement("a");
            const query = queryString.stringify(this.filter, {
                arrayFormat: "index",
            });
            a.href = this.webUrl + "/rekap/jadwal/export?" + query;
            a.setAttribute("target", "_blank");
            a.click();

    //         const url = window.URL.createObjectURL(new Blob([response.data]));
    //   const link = document.createElement('a');
    //   link.href = url;
    //   link.setAttribute('download', 'best_attendances.xlsx');
    //   document.body.appendChild(link);
    //   link.click();
        },
        onCheckMultiple() {
            let filterUnit = Object.values(this.filter.unit);
            if (filterUnit.includes(-1)) {
                this.filter.unit = [-1];
                console.log(this.filter.unit);
            }
        },
    },
};
</script>
