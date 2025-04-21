<script>
import NavBar from "@/components/nav-bar";
import RightBar from "@/components/right-bar";
import Footer from "@/components/footer";

export default {
    watch: {
        $route: {
            handler: "onRoutechange",
            immediate: true,
            deep: true,
        },
    },
    methods: {
        onRoutechange(ele) {
            this.initActiveMenu(ele.path);
        },
        initActiveMenu(ele) {
            setTimeout(() => {
                if (document.querySelector("#navbar-nav")) {
                    let a = document
                        .querySelector("#navbar-nav")
                        .querySelector('[href="' + ele + '"]');

                    if (a) {
                        a.classList.add("active");
                        let parentCollapseDiv = a.closest(
                            ".collapse.menu-dropdown"
                        );
                        if (parentCollapseDiv) {
                            parentCollapseDiv.classList.add("show");
                            parentCollapseDiv.parentElement.children[0].classList.add(
                                "active"
                            );
                            parentCollapseDiv.parentElement.children[0].setAttribute(
                                "aria-expanded",
                                "true"
                            );
                            if (
                                parentCollapseDiv.parentElement.closest(
                                    ".collapse.menu-dropdown"
                                )
                            ) {
                                parentCollapseDiv.parentElement
                                    .closest(".collapse")
                                    .classList.add("show");
                                if (
                                    parentCollapseDiv.parentElement.closest(
                                        ".collapse"
                                    ).previousElementSibling
                                )
                                    parentCollapseDiv.parentElement
                                        .closest(".collapse")
                                        .previousElementSibling.classList.add(
                                            "active"
                                        );
                            }
                        }
                    }
                }
            }, 1000);
        },
    },
    mounted() {
        if (document.querySelectorAll(".navbar-nav .collapse")) {
            let collapses = document.querySelectorAll(".navbar-nav .collapse");
            collapses.forEach((collapse) => {
                // Hide sibling collapses on `show.bs.collapse`
                collapse.addEventListener("show.bs.collapse", (e) => {
                    e.stopPropagation();
                    let closestCollapse =
                        collapse.parentElement.closest(".collapse");
                    if (closestCollapse) {
                        let siblingCollapses =
                            closestCollapse.querySelectorAll(".collapse");
                        siblingCollapses.forEach((siblingCollapse) => {
                            if (siblingCollapse.classList.contains("show")) {
                                siblingCollapse.classList.remove("show");
                                siblingCollapse.parentElement.firstChild.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );
                            }
                        });
                    } else {
                        let getSiblings = (elem) => {
                            // Setup siblings array and get the first sibling
                            let siblings = [];
                            let sibling = elem.parentNode.firstChild;
                            // Loop through each sibling and push to the array
                            while (sibling) {
                                if (
                                    sibling.nodeType === 1 &&
                                    sibling !== elem
                                ) {
                                    siblings.push(sibling);
                                }
                                sibling = sibling.nextSibling;
                            }
                            return siblings;
                        };
                        let siblings = getSiblings(collapse.parentElement);
                        siblings.forEach((item) => {
                            if (item.childNodes.length > 2) {
                                item.firstElementChild.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );
                                item.firstElementChild.classList.remove(
                                    "active"
                                );
                            }
                            let ids = item.querySelectorAll("*[id]");
                            ids.forEach((item1) => {
                                item1.classList.remove("show");
                                item1.parentElement.firstChild.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );
                                item1.parentElement.firstChild.classList.remove(
                                    "active"
                                );
                                if (item1.childNodes.length > 2) {
                                    let val = item1.querySelectorAll("ul li a");
                                    val.forEach((subitem) => {
                                        if (
                                            subitem.hasAttribute(
                                                "aria-expanded"
                                            )
                                        )
                                            subitem.setAttribute(
                                                "aria-expanded",
                                                "false"
                                            );
                                    });
                                }
                            });
                        });
                    }
                });
                // Hide nested collapses on `hide.bs.collapse`
                collapse.addEventListener("hide.bs.collapse", (e) => {
                    e.stopPropagation();
                    let childCollapses = collapse.querySelectorAll(".collapse");
                    childCollapses.forEach((childCollapse) => {
                        let childCollapseInstance = childCollapse;
                        childCollapseInstance.classList.remove("show");
                        childCollapseInstance.parentElement.firstChild.setAttribute(
                            "aria-expanded",
                            "false"
                        );
                    });
                });
            });
        }
    },
    components: {
        NavBar,
        RightBar,
        Footer,
    },
    computed: {
        myRole() {
            return this.$store.state?.auth?.data?.role;
        },
    },
};
</script>

<template>
    <div>
        <div id="layout-wrapper">
            <NavBar />
            <!-- ========== App Menu ========== -->
            <div class="app-menu navbar-menu">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    Test
                    <BButton
                        size="sm"
                        class="p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                        id="vertical-hover"
                    >
                        <i class="ri-record-circle-line"></i>
                    </BButton>
                </div>
                <div id="scrollbar">
                    <BContainer fluid>
                        <ul class="navbar-nav h-100" id="navbar-nav">
                            <li class="menu-title">
                                <span data-key="t-menu">
                                    {{ $t("t-menu") }}</span
                                >
                            </li>

                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <router-link
                                    class="nav-link menu-link"
                                    :to="{ name: 'Dashboard' }"
                                >
                                    <i class="ri-honour-line"></i>
                                    <span data-key="t-dashboards">{{
                                        $t("t-dashboards")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <router-link
                                    class="nav-link menu-link"
                                    to="/presensi"
                                >
                                    <i class="ri-user-location-fill"></i>
                                    <span data-key="t-presensi">{{
                                        $t("t-presensi")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <router-link
                                    class="nav-link menu-link"
                                    to="/jadwal-ku/list"
                                >
                                    <i class="ri-calendar-check-fill"></i>
                                    <span data-key="t-jadwalku">{{
                                        $t("t-jadwalku")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <router-link
                                    class="nav-link menu-link"
                                    to="/izin-ku"
                                >
                                    <i class="ri-walk-fill"></i>
                                    <span data-key="t-izinku">{{
                                        $t("t-izinku")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <router-link
                                    class="nav-link menu-link"
                                    :to="{ name: 'TukarShift' }"
                                >
                                    <i class="ri-calendar-event-fill"></i>
                                    <span data-key="t-shiftku">{{
                                        $t("t-shiftku")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <BLink
                                    class="nav-link menu-link"
                                    href="#sidebarJadwal"
                                    data-bs-toggle="collapse"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="sidebarSetting"
                                >
                                    <i class="ri-calendar-fill"></i>
                                    <span data-key="t-jadwal">
                                        {{ $t("t-jadwal") }}</span
                                    >
                                </BLink>
                                <div
                                    class="collapse menu-dropdown"
                                    id="sidebarJadwal"
                                >
                                    <ul class="nav nav-sm flex-column">
                                        <li
                                            v-if="
                                                [
                                                    'SUPER_ADMIN',
                                                    'STAF',
                                                    'KEPALA',
                                                    'KABID',
                                                    'KASUB',
                                                ].includes(this.myRole)
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/jadwal/list"
                                                class="nav-link custom-abc"
                                                data-key="t-jadwal-main"
                                            >
                                                {{ $t("t-jadwal-main") }}
                                            </router-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li
                                v-if="['SUPER_ADMIN'].includes(this.myRole)"
                                class="nav-item"
                            >
                                <BLink
                                    class="nav-link menu-link"
                                    href="#sidebarMaster"
                                    data-bs-toggle="collapse"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="sidebarMaster"
                                >
                                    <i class="ri-dashboard-2-line"></i>
                                    <span data-key="t-master">
                                        {{ $t("t-master") }}</span
                                    >
                                </BLink>
                                <div
                                    class="collapse menu-dropdown"
                                    id="sidebarMaster"
                                >
                                    <ul class="nav nav-sm flex-column">
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-karyawan"
                                                class="nav-link custom-abc"
                                                data-key="t-karyawan"
                                            >
                                                {{ $t("t-karyawan") }}
                                            </router-link>
                                        </li>
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-shift"
                                                class="nav-link custom-abc"
                                                data-key="t-shift"
                                            >
                                                {{ $t("t-shift") }}
                                            </router-link>
                                        </li>
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-izin"
                                                class="nav-link custom-abc"
                                                data-key="t-izin"
                                            >
                                                {{ $t("t-izin") }}
                                            </router-link>
                                        </li>
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-unit"
                                                class="nav-link custom-abc"
                                                data-key="t-unit"
                                            >
                                                {{ $t("t-unit") }}
                                            </router-link>
                                        </li>
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-jabatan"
                                                class="nav-link custom-abc"
                                                data-key="t-jabatan"
                                            >
                                                {{ $t("t-jabatan") }}
                                            </router-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li
                                v-if="['SUPER_ADMIN'].includes(this.myRole)"
                                class="nav-item"
                            >
                                <BLink
                                    class="nav-link menu-link"
                                    href="#sidebarSetting"
                                    data-bs-toggle="collapse"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="sidebarSetting"
                                >
                                    <i class="ri-settings-fill"></i>
                                    <span data-key="t-setting">
                                        {{ $t("t-setting") }}</span
                                    >
                                </BLink>
                                <div
                                    class="collapse menu-dropdown"
                                    id="sidebarSetting"
                                >
                                    <ul class="nav nav-sm flex-column">
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/m-lokasi"
                                                class="nav-link custom-abc"
                                                data-key="t-analytics"
                                            >
                                                {{ $t("t-lokasi") }}
                                            </router-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li
                                v-if="
                                    [
                                        'SUPER_ADMIN',
                                        'STAF',
                                        'KEPALA',
                                        'KABID',
                                        'KASUB',
                                    ].includes(this.myRole)
                                "
                                class="nav-item"
                            >
                                <BLink
                                    class="nav-link menu-link"
                                    href="#sidebarHistory"
                                    data-bs-toggle="collapse"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="sidebarHistory"
                                >
                                    <i class="ri-history-fill"></i>
                                    <span data-key="t-history">
                                        {{ $t("t-history") }}</span
                                    >
                                </BLink>
                                <div
                                    class="collapse menu-dropdown"
                                    id="sidebarHistory"
                                >
                                    <ul class="nav nav-sm flex-column">
                                        <li
                                            v-if="
                                                [
                                                    'SUPER_ADMIN',
                                                    'STAF',
                                                    'KEPALA',
                                                    'KABID',
                                                    'KASUB',
                                                ].includes(this.myRole)
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/history/presensi"
                                                class="nav-link custom-abc"
                                                data-key="t-history-presensi"
                                            >
                                                {{ $t("t-history-presensi") }}
                                            </router-link>
                                        </li>
                                        <li
                                            v-if="
                                                [
                                                    'SUPER_ADMIN',
                                                    'KEPALA',
                                                    'KABID',
                                                    'KASUB',
                                                ].includes(this.myRole)
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/history/tukar-shift"
                                                class="nav-link custom-abc"
                                                data-key="t-history-tukarshift"
                                            >
                                                {{ $t("t-history-tukarshift") }}
                                            </router-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li
                                v-if="['SUPER_ADMIN'].includes(this.myRole)"
                                class="nav-item"
                            >
                                <BLink
                                    class="nav-link menu-link"
                                    href="#sidebarRekap"
                                    data-bs-toggle="collapse"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="sidebarRekap"
                                >
                                    <i
                                        class="ri-git-repository-commits-fill"
                                    ></i>
                                    <span data-key="t-rekap">
                                        {{ $t("t-rekap") }}</span
                                    >
                                </BLink>
                                <div
                                    class="collapse menu-dropdown"
                                    id="sidebarRekap"
                                >
                                    <ul class="nav nav-sm flex-column">
                                        <li
                                            v-if="
                                                ['SUPER_ADMIN'].includes(
                                                    this.myRole
                                                )
                                            "
                                            class="nav-item"
                                        >
                                            <router-link
                                                to="/rekap/presensi"
                                                class="nav-link custom-abc"
                                                data-key="t-rekap-presensi"
                                            >
                                                {{ $t("t-rekap-presensi") }}
                                            </router-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </BContainer>
                    <!-- Sidebar -->
                </div>
                <!-- Left Sidebar End -->
                <!-- Vertical Overlay-->
                <div class="vertical-overlay"></div>
            </div>
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="main-content">
                <div class="page-content">
                    <!-- Start Content-->
                    <BContainer fluid>
                        <slot />
                    </BContainer>
                </div>
                <Footer />
            </div>
            <RightBar />
        </div>
    </div>
</template>