<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <router-link to="/" class="logo logo-dark">
                            <span class="logo-sm">
                                <img
                                    src="@/assets/images/logo_new.png"
                                    alt=""
                                    height="22"
                                />
                            </span>
                            <span class="logo-lg">
                                <img
                                    src="@/assets/images/logo_new.png"
                                    alt=""
                                    height="25"
                                />
                            </span>
                            Absensi CKI
                        </router-link>

                        <router-link to="/" class="logo logo-light">
                            <span class="logo-sm">
                                <img
                                    src="@/assets/images/logo_new.png"
                                    alt=""
                                    height="22"
                                />
                            </span>
                            <span class="logo-lg">
                                <img
                                    src="@/assets/images/logo_new.png"
                                    alt=""
                                    height="25"
                                />
                                Absensi CKI
                            </span>
                        </router-link>
                    </div>

                    <BButton
                        variant="white"
                        class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                        id="topnav-hamburger-icon"
                    >
                        <span class="hamburger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </BButton>
                </div>

                <div class="d-flex align-items-center">
                    <div class="ms-1 header-item d-sm-flex">
                        <OnlineOfflineStatus />
                    </div>
                    <div class="ms-1 header-item d-sm-flex">
                        <BButton
                            type="button"
                            variant="ghost-secondary"
                            class="btn-icon btn-topbar rounded-circle"
                            data-toggle="fullscreen"
                            @click="initFullScreen"
                        >
                            <i class="ri-fullscreen-fill fs-22"></i>
                        </BButton>
                    </div>

                    <div class="ms-1 header-item d-sm-flex">
                        <BButton
                            type="button"
                            variant="ghost-secondary"
                            class="btn-icon btn-topbar rounded-circle light-dark-mode"
                            @click="toggleDarkMode"
                        >
                            <i class="ri-moon-line fs-22"></i>
                        </BButton>
                    </div>

                    <BDropdown
                        variant="link"
                        class="ms-sm-3 header-item topbar-user"
                        toggle-class="rounded-circle arrow-none"
                        menu-class="dropdown-menu-end"
                        :offset="{
                            alignmentAxis: -14,
                            crossAxis: 0,
                            mainAxis: 0,
                        }"
                    >
                        <template #button-content>
                            <span class="d-flex align-items-center">
                                <div v-if="karyawan?.photo !== undefined">
                                    <img
                                        :src="
                                            getProfil(user.nip) +
                                            '?rnd=' +
                                            new Date()
                                        "
                                        alt="user-img"
                                        class="rounded-circle header-profile-user"
                                    />
                                </div>
                                <div v-else>
                                    <img
                                        class="rounded-circle header-profile-user"
                                        :src="`https://ui-avatars.com/api/?background=random&color=fff&name=${user.nama}`"
                                        alt="Header Avatar"
                                    />
                                </div>
                                <span class="text-start ms-xl-2">
                                    <span
                                        class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"
                                    >
                                        {{ `${user.nama} (${user.role})` }}
                                    </span>
                                    <span
                                        class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"
                                    >
                                        {{ user.role }}
                                    </span>
                                </span>
                            </span>
                        </template>
                        <h6 class="dropdown-header">
                            Welcome {{ user.nama }}!
                        </h6>
                        <router-link
                            :to="{
                                name: 'ProfileKaryawan',
                                params: { nip: user.nip },
                            }"
                            class="dropdown-item"
                            ><i
                                class="ri-shield-user-fill text-muted fs-16 align-middle me-1"
                            ></i>
                            <span class="align-middle"> Profil</span>
                        </router-link>
                        <a
                            href="javascript(0)"
                            class="dropdown-item"
                            @click.prevent="changePassword"
                            ><i
                                class="ri-key-2-fill text-muted fs-16 align-middle me-1"
                            ></i>
                            <span class="align-middle"> Ganti Password</span>
                        </a>
                        <a
                            v-if="platform === 'android'"
                            href="javascript(0)"
                            @click.prevent="$refs.liveUpdateRef.showModal()"
                            class="dropdown-item"
                        >
                            <i
                                class="ri-refresh-fill text-muted fs-18"
                                v-b-tooltip="'Cek Pembaruan Aplikasi'"
                            ></i>
                            Cek Update App
                        </a>
                        <a href="javascript(0)" class="dropdown-item">
                            <i
                                class="ri-device-line text-muted fs-16 align-middle me-1"
                            ></i>
                            <span class="align-middle">
                                Device
                                <i>ID </i>
                                <span class="text-primary">
                                    {{ deviceId }}
                                </span>
                            </span>
                        </a>
                        <a
                            href="javascript(0)"
                            class="dropdown-item"
                            @click.prevent="onLogout"
                            ><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"
                            ></i>
                            <span class="align-middle" data-key="t-logout">
                                Logout</span
                            >
                        </a>
                    </BDropdown>
                </div>
            </div>
        </div>
        <GantiPassword ref="gantiPassRef" />
        <SessionDevice ref="sessionDevRef" />
        <LiveUpdate ref="liveUpdateRef" />
    </header>
</template>
<script>
import { layoutMethods, authMethods, toastMethods } from "@/state/helpers";
import i18n from "../i18n";
import { authService } from "@/services/AuthService";
import Cookies from "js-cookie";
import GantiPassword from "@/views/auth/GantiPassword.vue";
import { webUrl } from "@/config/http";
import { useTime } from "vue-timer-hook";
import SessionDevice from "@/views/account/SessionDevice.vue";
import OnlineOfflineStatus from "./OnlineOfflineStatus.vue";
import { defineAsyncComponent } from "vue";
// import { Device } from "@capacitor/device";

/**
 * Nav-bar Component
 */
export default {
    components: {
        GantiPassword,
        SessionDevice,
        OnlineOfflineStatus,
        LiveUpdate: defineAsyncComponent(() => import("./live-update.vue")),
    },
    data() {
        const user = this.$store.state.auth.data;
        let format = "24-hour";
        return {
            user,
            lan: i18n.locale,
            text: null,
            flag: null,
            value: null,
            myVar: 1,
            time: useTime(format),
            deviceId: localStorage.getItem("deviceId"),
            karyawan: null,
            platform: "web",
        };
    },
    computed: {
        calculateTotalPrice() {
            return this.cartItems
                .reduce((total, item) => total + parseFloat(item.itemPrice), 0)
                .toFixed(2);
        },
    },
    mounted() {
        document.addEventListener("scroll", function () {
            var pageTopbar = document.getElementById("page-topbar");
            if (pageTopbar) {
                document.body.scrollTop >= 50 ||
                document.documentElement.scrollTop >= 50
                    ? pageTopbar.classList.add("topbar-shadow")
                    : pageTopbar.classList.remove("topbar-shadow");
            }
        });
        if (document.getElementById("topnav-hamburger-icon")) {
            document
                .getElementById("topnav-hamburger-icon")
                .addEventListener("click", this.toggleHamburgerMenu);
        }
    },
    // async created() {
    //     let devInfo = await Device.getInfo();
    //     this.platform = devInfo.platform;
    // },
    methods: {
        ...layoutMethods,
        ...authMethods,
        ...toastMethods,
        toggleHamburgerMenu() {
            var windowSize = document.documentElement.clientWidth;
            let layoutType =
                document.documentElement.getAttribute("data-layout");

            document.documentElement.setAttribute(
                "data-sidebar-visibility",
                "show"
            );
            let visiblilityType = document.documentElement.getAttribute(
                "data-sidebar-visibility"
            );

            if (windowSize > 767)
                document
                    .querySelector(".hamburger-icon")
                    .classList.toggle("open");

            //For collapse horizontal menu
            if (
                document.documentElement.getAttribute("data-layout") ===
                "horizontal"
            ) {
                document.body.classList.contains("menu")
                    ? document.body.classList.remove("menu")
                    : document.body.classList.add("menu");
            }

            //For collapse vertical menu

            if (
                visiblilityType === "show" &&
                (layoutType === "vertical" || layoutType === "semibox")
            ) {
                if (windowSize < 1025 && windowSize > 767) {
                    document.body.classList.remove("vertical-sidebar-enable");
                    document.documentElement.getAttribute(
                        "data-sidebar-size"
                    ) == "sm"
                        ? document.documentElement.setAttribute(
                              "data-sidebar-size",
                              ""
                          )
                        : document.documentElement.setAttribute(
                              "data-sidebar-size",
                              "sm"
                          );
                } else if (windowSize > 1025) {
                    document.body.classList.remove("vertical-sidebar-enable");
                    document.documentElement.getAttribute(
                        "data-sidebar-size"
                    ) == "lg"
                        ? document.documentElement.setAttribute(
                              "data-sidebar-size",
                              "sm"
                          )
                        : document.documentElement.setAttribute(
                              "data-sidebar-size",
                              "lg"
                          );
                } else if (windowSize <= 767) {
                    document.body.classList.add("vertical-sidebar-enable");
                    document.documentElement.setAttribute(
                        "data-sidebar-size",
                        "lg"
                    );
                }
            }
        },
        toggleMenu() {
            this.$parent.toggleMenu();
        },
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar();
        },
        initFullScreen() {
            document.body.classList.toggle("fullscreen-enable");
            if (
                !document.fullscreenElement &&
                /* alternative standard method */
                !document.mozFullScreenElement &&
                !document.webkitFullscreenElement
            ) {
                // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(
                        Element.ALLOW_KEYBOARD_INPUT
                    );
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        },
        toggleDarkMode() {
            if (
                document.documentElement.getAttribute("data-bs-theme") == "dark"
            ) {
                document.documentElement.setAttribute("data-bs-theme", "light");
            } else {
                document.documentElement.setAttribute("data-bs-theme", "dark");
            }

            const mode = document.documentElement.getAttribute("data-bs-theme");
            this.changeMode({
                mode: mode,
            });
            this.changeSidebarColor({
                sidebarColor: mode,
            });
            this.changeTopbar({
                topbar: mode,
            });
            localStorage.setItem(
                "resetValue",
                JSON.stringify(this.$store.state.layout)
            );
        },
        async onLogout() {
            const [err] = await authService.logoutOne();
            if (err) {
                this.toastError({
                    title: "Gagal",
                    msg: err.response?.data?.errors,
                });
                this.isLoading = false;

                return;
            }
            Cookies.remove("cki-absen");
            this.logout();
            this.$router.go({ name: "Login" });
        },
        changePassword() {
            this.$refs.gantiPassRef.showModal();
        },
        viewSesi(type, backdropType) {
            this.$refs.sessionDevRef.backdropOff = true;
            this.$refs.sessionDevRef.deviceId = this.deviceId;
            if (type == false) {
                this.$refs.sessionDevRef.backdrop = backdropType;
            } else {
                this.$refs.sessionDevRef.backdrop = backdropType;
            }
        },
        getProfil(nip) {
            return `${webUrl}/profil/${nip}`;
        },
    },
};
</script>
