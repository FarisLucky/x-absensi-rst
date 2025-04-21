<script>
import router from "@/router";
import simplebar from "simplebar-vue";
import { layoutComputed, layoutMethods } from "@/state/helpers";

import NavBar from "@/components/nav-bar";
import Menu from "@/components/menu.vue";
import RightBar from "@/components/right-bar";
import Footer from "@/components/footer";
localStorage.setItem("hoverd", false);

/**
 * Vertical layout
 */
export default {
    components: { NavBar, Footer, Menu, simplebar, RightBar },
    data() {
        return {
            isMenuCondensed: false,
        };
    },
    computed: {
        ...layoutComputed,
    },
    created: function () {
        document.body.removeAttribute("data-layout", "horizontal");
        document.body.removeAttribute("data-topbar", "dark");
        document.body.removeAttribute("data-layout-size", "boxed");
        this.updateSidebarSize();
    },
    methods: {
        ...layoutMethods,
        updateSidebarSize() {
            let sidebarSize = "";
            // Check window.screen.width and update the data-sidebar-size attribute
            if (window.innerWidth < 1025) {
                this.changeSidebarSize("sm");
                // this.sidebarSize = "sm";
                sidebarSize = "sm";
            } else {
                this.changeSidebarSize("lg");
                // this.sidebarSize = "lg"; // Reset sidebarSize if screen width is >= 1025
                sidebarSize = "lg";
            }
            // Update the data-sidebar-size attribute of document.documentElement
            document.documentElement.setAttribute(
                "data-sidebar-size",
                sidebarSize
            );
        },

        initActiveMenu() {
            if (
                document.documentElement.getAttribute("data-sidebar-size") ===
                "sm-hover"
            ) {
                localStorage.setItem("hoverd", true);
                document.documentElement.setAttribute(
                    "data-sidebar-size",
                    "sm-hover-active"
                );
            } else if (
                document.documentElement.getAttribute("data-sidebar-size") ===
                "sm-hover-active"
            ) {
                localStorage.setItem("hoverd", false);
                document.documentElement.setAttribute(
                    "data-sidebar-size",
                    "sm-hover"
                );
            } else {
                document.documentElement.setAttribute(
                    "data-sidebar-size",
                    "sm-hover"
                );
            }
        },
        toggleMenu() {
            document.body.classList.toggle("sidebar-enable");
            if (window.screen.width >= 992) {
                // eslint-disable-next-line no-unused-vars
                router.afterEach((routeTo, routeFrom) => {
                    document.body.classList.remove("sidebar-enable");
                    document.body.classList.remove("vertical-collpsed");
                });
                document.body.classList.toggle("vertical-collpsed");
            } else {
                // eslint-disable-next-line no-unused-vars
                router.afterEach((routeTo, routeFrom) => {
                    document.body.classList.remove("sidebar-enable");
                });
                document.body.classList.remove("vertical-collpsed");
            }
            this.isMenuCondensed = !this.isMenuCondensed;
        },
        toggleRightSidebar() {
            document.body.classList.toggle("right-bar-enabled");
        },
        hideRightSidebar() {
            document.body.classList.remove("right-bar-enabled");
        },
    },
    mounted() {
        if (localStorage.getItem("hoverd") == "true") {
            document.documentElement.setAttribute(
                "data-sidebar-size",
                "sm-hover-active"
            );
        }

        document.getElementById("overlay").addEventListener("click", () => {
            document.body.classList.remove("vertical-sidebar-enable");
        });
        if (window.screen.width < 1025) {
            document.documentElement.setAttribute("data-sidebar-size", "sm");
        }

        window.addEventListener("resize", () => {
            document.body.classList.remove("vertical-sidebar-enable");
            document.querySelector(".hamburger-icon").classList.add("open");
            this.updateSidebarSize();
        });
    },
    unmounted() {
        window.removeEventListener("resize", this.updateSidebarSize);
    },
};
</script>

<template>
    <div id="layout-wrapper">
        <NavBar />
        <div>
            <!-- ========== Left Sidebar Start ========== -->
            <!-- ========== App Menu ========== -->
            <div class="app-menu navbar-menu">
                <!-- LOGO -->
                <div class="navbar-brand-box text-center">
                    <!-- Dark Logo-->
                    <router-link to="/" class="logo logo-dark">
                        <span class="logo-sm text-center">
                            <img
                                src="@/assets/images/logo_new.png"
                                height="40"
                                class="ms-1"
                            />
                        </span>
                        <span class="logo-lg">
                            <img
                                src="@/assets/images/logo_new.png"
                                height="50"
                                class="ms-1 me-2"
                            />
                        </span>
                        <h4 class="fs-14">Absensi PT CKI</h4>
                    </router-link>
                    <!-- Light Logo-->
                    <router-link to="/" class="logo logo-light">
                        <span class="logo-sm text-center">
                            <img
                                src="@/assets/images/logo_new.png"
                                alt=""
                                height="40"
                                class="ms-1"
                            />
                        </span>
                        <span class="logo-lg">
                            <img
                                src="@/assets/images/logo_new.png"
                                alt=""
                                height="50"
                                class="ms-1 me-2"
                            />
                        </span>
                        <h4 class="fs-14">Absensi PT CKI</h4>
                    </router-link>
                    <BButton
                        size="sm"
                        class="p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                        id="vertical-hover"
                        @click="initActiveMenu"
                    >
                        <i class="ri-record-circle-line"></i>
                    </BButton>
                </div>

                <simplebar id="scrollbar" class="h-100" ref="scrollbar">
                    <Menu></Menu>
                </simplebar>
                <div class="sidebar-background"></div>
            </div>
            <!-- Left Sidebar End -->
            <!-- Vertical Overlay-->
            <div class="vertical-overlay" id="overlay"></div>
        </div>
        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="main-content">
            <div class="smooth-scroll-container" ref="container">
                <div class="page-content position-relative">
                    <div
                        style="
                            min-height: 230px;
                            position: absolute;
                            left: 0;
                            right: 0;
                            top: 0;
                            border-bottom-left-radius: 30px;
                            border-bottom-right-radius: 30px;
                            background: rgb(14, 56, 122);
                            background: linear-gradient(
                                56deg,
                                rgba(14, 56, 122, 1) 41%,
                                rgba(159, 175, 202, 1) 100%
                            );
                        "
                    ></div>
                    <!-- Start Content-->
                    <BContainer fluid class="p-0">
                        <slot />
                    </BContainer>
                </div>
            </div>
            <Footer />
            <RightBar />
        </div>
    </div>
</template>
<style scoped>
.sidebar-user {
    background-color: #a7f3d0;
    display: block;
}

.smooth-scroll-container {
    scroll-behavior: smooth;
    overflow-y: auto;
    height: 100vh;
    /* scroll-snap-type: y mandatory; */
}

/* Optional: Snap points */
.smooth-scroll-container > * {
    scroll-snap-align: start;
}
</style>
