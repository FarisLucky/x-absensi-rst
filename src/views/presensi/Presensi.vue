<template>
    <div class="position-relative">
        <div class="bg-white rounded p-2">
            <div class="bg-layer text-center">
                <div
                    v-if="lokasi.length > 0"
                    style="height: 70vh; width: 100%; margin-bottom: 0.7rem"
                >
                    <l-map
                        :zoom="zoom"
                        :fade-animation="true"
                        :center="[lokasi[0].latitude, lokasi[0].longitude]"
                        :marker-zoom-animation="true"
                        ref="mapLeaflet"
                        :minZoom="10"
                        :maxZoom="19"
                        @ready="onMapGetReady"
                    >
                        <l-tile-layer
                            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        ></l-tile-layer>
                        <l-control-layers />
                        <l-marker :lat-lng="[lat, lng]">
                            <l-tooltip> lol </l-tooltip>
                        </l-marker>
                        <l-circle
                            v-for="(lok, idx) in lokasi"
                            :key="idx"
                            :name="lok.nama"
                            :lat-lng="[lok.latitude, lok.longitude]"
                            :radius="lok.radius"
                            :fill="true"
                            color="#F06A4E"
                        >
                            <l-popup
                                >{{ lok.nama }} - Radius:
                                {{ lok.radius }} m</l-popup
                            >
                        </l-circle>
                    </l-map>
                </div>
            </div>
            <div
                style="
                    position: absolute;
                    top: 7%;
                    right: 10%;
                    z-index: 999;
                    text-align: center;
                "
            >
                <BButtonGroup>
                    <BDropdown
                        size="md"
                        variant="light"
                        text="Lihat Lokasi Absen"
                    >
                        <BDropdownItem
                            v-for="(lok, idx) in lokasi"
                            :key="idx"
                            @click.prevent="flyToLocation(lok)"
                        >
                            {{ lok.nama }}
                        </BDropdownItem>
                    </BDropdown>
                </BButtonGroup>
            </div>
            <div
                style="
                    position: absolute;
                    bottom: 20%;
                    z-index: 999;
                    width: 100%;
                    text-align: center;
                "
            >
                <div
                    v-if="devicePlatform === 'android' && detectFakeGps"
                    class="bg-white rounded p-2 d-inline-block"
                >
                    <div class="alert alert-secondary">
                        <h5 class="mb-1">
                            Anda Terdeteksi Menggunakan FAKE GPS
                        </h5>
                    </div>
                </div>
                <div
                    v-else-if="!inRadius"
                    class="bg-white rounded p-2 d-inline-block"
                >
                    <div class="alert alert-secondary">
                        <b>Anda diluar Radius Absen</b>
                        <p class="mb-1">Lokasi Anda Saat Ini:</p>
                        <span>
                            {{ address }}
                        </span>
                    </div>
                    <div class="text-center">
                        <BButton
                            variant="danger"
                            size="sm"
                            @click.prevent="onMapGetReady"
                        >
                            <i class="ri-play-circle-line"></i>
                            Cek Ulang Lokasi Saya
                        </BButton>
                    </div>
                </div>
                <div v-else class="bg-white rounded p-2 d-inline-block">
                    <h5 class="mb-1">Silahkan Masuk</h5>
                    <div class="text-center">
                        <BButton
                            variant="primary"
                            @click.prevent="
                                () => {
                                    getJadwal();
                                    openBottomSheet();
                                }
                            "
                        >
                            <i class="ri-play-circle-line"></i>
                            Mulai Absen
                        </BButton>
                    </div>
                </div>
            </div>
        </div>
        <notif-success ref="notifSuccessRef" />
        <vue-bottom-sheet ref="myBottomSheet">
            <div style="height: 65vh; padding: 1rem 1.5rem">
                <div v-if="jadwal !== null">
                    <BRow
                        class="g-2 justify-contents-between align-items-center"
                    >
                        <BCol cols="6">
                            <div class="mb-1">
                                <span
                                    class="fs-12"
                                    :class="{
                                        'badge badge-gradient-secondary':
                                            jadwal.status === null,
                                        'badge badge-gradient-info':
                                            jadwal.status ===
                                            JADWAL_STATUS.PROGRESS,
                                        'badge badge-gradient-primary':
                                            jadwal.status ===
                                            JADWAL_STATUS.SELESAI,
                                        'badge badge-gradient-danger':
                                            jadwal.status ===
                                            JADWAL_STATUS.TIDAK_HADIR,
                                        'badge badge-gradient-warning':
                                            jadwal.status ===
                                            JADWAL_STATUS.IZIN,
                                    }"
                                >
                                    {{ jadwal.status_cast }}
                                </span>
                            </div>
                        </BCol>
                        <BCol cols="6">
                            <div class="text-end mb-2">
                                <BButton
                                    @click.prevent="
                                        () => {
                                            usingFakeGPS();
                                            refresh();
                                        }
                                    "
                                    variant="soft-secondary"
                                    size="sm"
                                >
                                    <i class="ri-refresh-fill"></i>
                                </BButton>
                            </div>
                        </BCol>
                        <BCol cols="12">
                            <div class="py-2">
                                <h4 class="text-center mb-1">
                                    <strong>
                                        <span>{{ time.hours }}</span
                                        >:<span>{{ time.minutes }}</span
                                        >:<span>{{ time.seconds }}</span>
                                    </strong>
                                </h4>
                                <p class="text-center mb-1">
                                    {{ jadwal?.tanggal_cast }}
                                </p>
                            </div>
                        </BCol>
                        <BCol cols="12">
                            <div data-simplebar style="max-height: 215px">
                                <ul class="list-group list-group-flush fs-10">
                                    <li class="list-group-item">
                                        <div
                                            v-if="
                                                devicePlatform === 'android' &&
                                                detectFakeGps
                                            "
                                        >
                                            <div class="p-2">
                                                <h5 class="fs-16 text-center">
                                                    {{ detectFakeGpsDesc }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div
                                                v-if="
                                                    (jadwal?.status == null ||
                                                        jadwal?.status == 1) &&
                                                    jadwal.libur < 1
                                                "
                                                class="text-muted"
                                            >
                                                <div v-if="gpsOn">
                                                    <h5
                                                        v-if="!inRadius"
                                                        class="rounded d-inline-block fs-11"
                                                    >
                                                        Diluar Jangkauan Radius,
                                                        Cek
                                                        <button
                                                            class="btn btn-sm btn-danger"
                                                            @click.prevent="
                                                                onMyLocation
                                                            "
                                                        >
                                                            <i
                                                                class="ri-gps-fill align-bottom"
                                                            ></i>
                                                            Lokasi Saya
                                                        </button>
                                                    </h5>
                                                    <div
                                                        v-else
                                                        class="text-center"
                                                    >
                                                        <BButton
                                                            v-if="
                                                                jadwal?.masuk ===
                                                                null
                                                            "
                                                            variant="success"
                                                            @click.prevent="
                                                                onMasuk
                                                            "
                                                            :disabled="
                                                                submitted
                                                            "
                                                            class="me-1 w-50"
                                                        >
                                                            <i
                                                                class="ri-user-location-fill align-bottom me-1"
                                                            ></i>
                                                            Masuk
                                                        </BButton>
                                                        <BButton
                                                            v-else-if="
                                                                jadwal?.masuk !==
                                                                    null &&
                                                                jadwal?.pulang ===
                                                                    null
                                                            "
                                                            variant="danger"
                                                            @click.prevent="
                                                                onPulang
                                                            "
                                                            :disabled="
                                                                submitted
                                                            "
                                                            class="w-50"
                                                        >
                                                            <i
                                                                class="ri-user-location-fill align-bottom me-1"
                                                            ></i>
                                                            Pulang
                                                        </BButton>
                                                    </div>
                                                </div>
                                                <p v-else class="mb-0 fs-11">
                                                    Pastikan mengaktifkan GPS
                                                    untuk absen. Kemudian muat
                                                    GPS
                                                    <a
                                                        href="javascript(0)"
                                                        class="btn btn-sm btn-danger ms-1"
                                                        @click.prevent="
                                                            onMyLocation
                                                        "
                                                    >
                                                        <i
                                                            class="ri-refresh-line"
                                                        ></i>
                                                        Klik disini
                                                    </a>
                                                </p>
                                            </div>
                                            <BAlert v-else variant="success">
                                                <strong>GPS Sudah Aktif</strong>
                                            </BAlert>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                >
                                                    <div
                                                        class="flex-shrink-1 avatar-xs"
                                                    >
                                                        <div
                                                            class="avatar-title bg-primary-subtle text-primary rounded"
                                                        >
                                                            <i
                                                                class="ri-hospital-fill"
                                                            ></i>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex-shrink-1 p-1"
                                                    >
                                                        <span
                                                            class="fs-12 text-muted mb-1"
                                                        >
                                                            Lokasi Absen:
                                                        </span>
                                                        <h6 class="fs-13 mb-1">
                                                            {{
                                                                lokasiPresensi?.nama
                                                            }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-success">
                                                    <i
                                                        class="ri-checkbox-circle-fill"
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                >
                                                    <div
                                                        class="flex-shrink-1 avatar-xs"
                                                    >
                                                        <div
                                                            class="avatar-title bg-primary-subtle text-primary rounded"
                                                        >
                                                            <i
                                                                class="ri-hospital-fill"
                                                            ></i>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex-shrink-1 p-1"
                                                    >
                                                        <span
                                                            class="fs-12 text-muted mb-1"
                                                        >
                                                            Jadwal:
                                                        </span>
                                                        <h6 class="fs-13 mb-1">
                                                            {{ jadwal?.shift }}
                                                            <span
                                                                class="text-muted ms-1"
                                                            >
                                                                ({{
                                                                    jadwal?.jam_masuk ??
                                                                    ""
                                                                }}
                                                                -
                                                                {{
                                                                    jadwal?.jam_pulang ??
                                                                    ""
                                                                }})
                                                                {{
                                                                    jadwal.kode_shift
                                                                }}
                                                            </span>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-success">
                                                    <i
                                                        class="ri-checkbox-circle-fill"
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                >
                                                    <div
                                                        class="flex-shrink-0 avatar-xs"
                                                    >
                                                        <div
                                                            class="avatar-title bg-primary-subtle text-primary rounded"
                                                        >
                                                            <i
                                                                class="ri-record-circle-fill"
                                                            ></i>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex-shrink-0 p-1"
                                                    >
                                                        <span
                                                            class="fs-12 text-muted mb-1 d-block"
                                                        >
                                                            Presensi:
                                                        </span>
                                                        <h6 class="fs-13 mb-1">
                                                            <template
                                                                v-if="
                                                                    jadwal?.status ===
                                                                    null
                                                                "
                                                            >
                                                                Masuk
                                                            </template>
                                                            <template
                                                                v-else-if="
                                                                    jadwal?.status ===
                                                                    2
                                                                "
                                                            >
                                                                Pulang
                                                            </template>
                                                            <template
                                                                v-else-if="
                                                                    jadwal?.status ===
                                                                    1
                                                                "
                                                            >
                                                                Selesai
                                                            </template>
                                                            <span
                                                                class="text-muted ms-1"
                                                            >
                                                                ({{
                                                                    jadwal?.masuk ??
                                                                    ""
                                                                }}
                                                                -
                                                                {{
                                                                    jadwal?.pulang ??
                                                                    ""
                                                                }})
                                                            </span>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="alert p-1"
                                                    :class="{
                                                        'alert-danger':
                                                            jadwal?.status_absen ===
                                                            'TELAT',
                                                        'alert-success':
                                                            jadwal?.status_absen ===
                                                            'TEPAT',
                                                    }"
                                                >
                                                    <span>
                                                        {{
                                                            jadwal?.status_absen
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <hr />
                                <BRow class="g-2">
                                    <BCol cols="6">
                                        <div class="rounded px-3 text-center">
                                            <h5 class="mb-1">Keterlambatan</h5>
                                            <h4 class="mb-1">
                                                {{ jadwal?.ttltelat }} M
                                            </h4>
                                        </div>
                                    </BCol>
                                    <BCol cols="6">
                                        <div class="rounded px-3 text-center">
                                            <h5 class="mb-1">
                                                Total Jam Kerja
                                            </h5>
                                            <h4 class="mb-1">
                                                {{
                                                    convertMinutes(
                                                        jadwal?.ttlkerja
                                                    )
                                                }}
                                            </h4>
                                        </div>
                                    </BCol>
                                </BRow>
                            </div>
                        </BCol>
                    </BRow>
                </div>
                <div v-else>
                    <h4 class="text-center fs-14">
                        <Lottie
                            colors="primary:#121331,secondary:#08a88a"
                            trigger="loop"
                            :options="{
                                animationData: animationData4,
                            }"
                            :height="70"
                            :width="70"
                        />
                        Belum ada Dinas
                    </h4>
                </div>
            </div>
        </vue-bottom-sheet>
        <NotifLocationDisabled
            :msg="gpsErrorMessage"
            @openSetting="openLocationSettings"
            @showMyLocation="onMapGetReady"
            ref="notifLocationDisabledRef"
        />
    </div>
</template>
<script>
import "leaflet/dist/leaflet.css";
import {
    LMap,
    LTileLayer,
    LMarker,
    LControlLayers,
    LTooltip,
    LPopup,
    LCircle,
} from "@vue-leaflet/vue-leaflet";
import { LatLng } from "leaflet";
import { presensiService } from "@/services/PresensiService";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { mLokasiService } from "@/services/MLokasiService";
import { Geolocation } from "@capacitor/geolocation";
import { required } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { ICM, ICT, ILL, IS, JADWAL_STATUS } from "@/helpers/utils";
import { defineAsyncComponent } from "vue";
import Lottie from "@/components/widgets/lottie.vue";
import animationData4 from "@/components/widgets/pithnlch.json";
import dayjs from "dayjs";
import { Location } from "capacitor-check-ismock-location";
import { Device } from "@capacitor/device";
import { useTime } from "vue-timer-hook";
import VueBottomSheet from "@webzlodimir/vue-bottom-sheet";
import "@webzlodimir/vue-bottom-sheet/dist/style.css";
import axios from "axios";
import { convertMinutesToHours } from "@/helpers/format";
import { NativeSettings, AndroidSettings } from "capacitor-native-settings";
import NotifLocationDisabled from "./NotifLocationDisabled.vue";

export default {
    components: {
        LMap,
        LTileLayer,
        LMarker,
        LControlLayers,
        LTooltip,
        LPopup,
        LCircle,
        NotifSuccess: defineAsyncComponent(() => import("./NotifSuccess.vue")),
        Lottie,
        VueBottomSheet,
        NotifLocationDisabled,
    },
    data() {
        return {
            zoom: 17,
            iconWidth: 25,
            iconHeight: 40,
            lat: "",
            lng: "",
            last_ip: "",
            deviceType: "web",
            devicePlatform: "",
            lokasi: [],
            lokasiPresensi: null,
            jadwal: null,
            presensiMasuk: null,
            presensiPulang: null,
            presensi: {
                manufact: "",
                model: "",
                platform: "",
                osVersion: "",
                nama: "",
                ip: "",
                id_jadwal: "",
            },
            inRadius: false,
            gpsOn: true,
            ICM,
            ICT,
            ILL,
            IS,
            animationData4,
            permissionState: null,
            gpsErrorMessage: null,
            detectFakeGps: false,
            detectFakeGpsDesc: "",
            submitted: false,
            time: useTime("24-hour"),
            address: "",
            JADWAL_STATUS,
        };
    },
    async created() {
        this.usingFakeGPS();
        this.refresh();
        this.getModelDevice();
    },
    setup() {
        return { v$: useVuelidate() };
    },
    validations() {
        return {
            lat: { required },
            lng: { required },
        };
    },
    methods: {
        ...toastMethods,
        ...spinnerMethods,
        flyToLocation(lokasi) {
            return this.$refs.mapLeaflet.leafletObject.flyTo(
                new LatLng(lokasi.latitude, lokasi.longitude),
                this.zoom
            );
        },
        refresh() {
            this.getLocation();
            this.getJadwal();
        },
        async onMapGetReady() {
            try {
                if (this.$refs.notifLocationDisabledRef.modal) {
                    this.$refs.notifLocationDisabledRef.hideModal();
                }
                this.usingFakeGPS();
                const permissionStatus = await Geolocation.checkPermissions();
                if (permissionStatus?.location != "granted") {
                    if (this.devicePlatform.platform === "android") {
                        const requestStatus =
                            await Geolocation.requestPermissions();
                        console.log("granted");
                        if (requestStatus.location != "granted") {
                            // go to location settings
                            await this.openLocationSettings(true);
                            return;
                        }
                    }
                    if (permissionStatus.location === "denied") {
                        this.toastError({
                            title: "Gagal",
                            msg: "Permintaan Lokasi ditolak user, reset permintaan",
                        });
                    }
                }
                let locationOpt = {
                    maximumAge: 0,
                    timeout: 10000,
                    enableHighAccuracy: true,
                };
                const pos = await Geolocation.getCurrentPosition(locationOpt);

                this.lat = pos.coords.latitude;
                this.lng = pos.coords.longitude;
                await this.reverseCoordinate();
                await this.flyToLocation({
                    latitude: this.lat,
                    longitude: this.lng,
                });
                await this.checkRadius();
                if (this.inRadius) {
                    this.openBottomSheet();
                }
            } catch (error) {
                if (error?.message === "Location services are not enabled") {
                    this.gpsErrorMessage = "Layanan Lokasi Belum diaktifkan";
                    this.$refs.notifLocationDisabledRef.showModal();
                    await this.openLocationSettings();
                }
                if (error?.message === "Location permission was denied") {
                    this.gpsErrorMessage =
                        "Reset Permintaan lokasi di pengaturan Aplikasi";
                    this.$refs.notifLocationDisabledRef.showModal();
                    await this.openLocationSettings();
                }
                console.log(error);
            }
        },
        openLocationSettings(app = false) {
            console.log("open settings...");
            return NativeSettings.open({
                optionAndroid: app
                    ? AndroidSettings.ApplicationDetails
                    : AndroidSettings.Location,
            });
        },
        async onMasuk() {
            this.onMapGetReady();
            const result = await this.v$.$validate();
            if (!result) {
                this.toastError({
                    title: "Gagal",
                    msg: "Cek LOKASI SAYA dulu",
                });
                return;
            }
            this.submitted = true;
            const [err, resp] = await presensiService.masuk({
                id_jadwal: this.jadwal.id,
                lok_masuk: this.lokasiPresensi,
                latlng_masuk: `${this.lat}/${this.lng}`,
                presensi: Object.assign(this.presensi, {
                    id_jadwal: this.jadwal.id,
                }),
            });
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.submitted = false;
                this.closeBottomSheet();

                return;
            }
            this.closeBottomSheet();
            this.$refs.notifSuccessRef.presensi = resp.data;
            this.$refs.notifSuccessRef.showModal();
            this.submitted = false;
        },
        async onPulang() {
            this.onMapGetReady();
            const result = await this.v$.$validate();
            if (!result) {
                this.toastError({
                    title: "Gagal",
                    msg: "Cek LOKASI SAYA dulu",
                });
                return;
            }
            this.submitted = true;
            const [err] = await presensiService.pulang({
                id_jadwal: this.jadwal.id,
                lok_pulang: this.lokasiPresensi,
                latlng_pulang: `${this.lat}/${this.lng}`,
                presensi: Object.assign(this.presensi, {
                    id_jadwal: this.jadwal.id,
                }),
            });
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.submitted = false;
                this.closeBottomSheet();

                return;
            }
            this.toastSuccess({
                title: "Berhasil",
                msg: "Presensi Berhasil",
            });
            this.closeBottomSheet();
            this.submitted = false;
            this.$router.push({ name: "HarianJadwal" });
        },
        async getLocation() {
            let aktif = 1;
            const [err, resp] = await mLokasiService.data(aktif);
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                return;
            }
            this.lokasi = resp.data;
        },
        async checkRadius() {
            let form = {
                latitude: this.lat,
                longitude: this.lng,
            };
            const [err, resp] = await presensiService.radiusValidation(form);
            if (err) {
                this.inRadius = false;
                return;
            }
            this.inRadius = true;
            this.lokasiPresensi = resp.data;
        },
        async getJadwal() {
            // show spinner
            this.show();
            const [err, resp] = await presensiService.jadwalByNip();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                // hide spinner
                this.hide();
                return;
            }
            this.jadwal = resp.data;
            this.hide();
        },
        setDateTime(datetime, latetime) {
            return dayjs(datetime, "YYYY-MM-DD HH:mm")
                .add(latetime, "minute")
                .format("HH:mm");
        },
        async usingFakeGPS() {
            let info = await Device.getInfo();
            this.devicePlatform = info.platform;
            if (info.platform === "android") {
                const isLastLocationMocked =
                    await Location.isLastLocationMocked();
                this.detectFakeGps = isLastLocationMocked.value;
                if (this.detectFakeGps) {
                    this.detectFakeGpsDesc = "ANDA TERDETEKSI FAKE GPS";
                } else {
                    this.detectFakeGpsDesc = "";
                }
            }
        },
        async getModelDevice() {
            let info = await Device.getInfo();
            this.presensi.manufact = info.manufacturer;
            this.presensi.model = info.model;
            this.presensi.platform = info.platform;
            this.presensi.osVersion = info.osVersion;
            this.presensi.nama = info.name;
            this.presensi.ip = this.last_ip;
        },
        hitungJamMenit(totalMenit) {
            const jam = Math.floor(totalMenit / 60);
            const menit = totalMenit % 60;

            return `${jam} jam ${menit} menit`;
        },
        openBottomSheet() {
            this.$refs.myBottomSheet?.open();
        },
        closeBottomSheet() {
            this.$refs.myBottomSheet?.close();
        },
        async reverseCoordinate() {
            if (this.lat !== "" && this.lng !== "") {
                const result = await axios.get(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}`
                );
                this.address = result?.data?.display_name;
            }
        },
        convertMinutes(val) {
            const { hours, remainingMinutes } = convertMinutesToHours(val);

            return `${hours} J ${remainingMinutes} M`;
        },
    },
};
</script>
<style scoped>
.offcanvas.offcanvas-bottom {
    height: 55vh;
    background-color: rgba(255, 255, 255, 0.8);
}
.list-group-item {
    background-color: rgba(255, 255, 255, 0.8);
    border: none;
}
.bottom-sheet__content {
    background-color: rgba(255, 255, 255, 0.4);
}
</style>
