<script>
// import { lemburService } from "@/services/LemburService";
import { layoutComputed } from "@/state/helpers";

export default {
  data() {
    return {
      settings: {
        minScrollbarLength: 60,
      },
      superAdminMenu: ["SUPER_ADMIN"],
    };
  },
  computed: {
    ...layoutComputed,
    layoutType: {
      get() {
        return this.$store ? this.$store.state.layout.layoutType : {} || {};
      },
    },
    isSuperAdmin() {
      return this.$route?.meta?.role.includes(
        this.$store.state?.auth?.data?.role
      );
    },
    myRole() {
      return this.$store.state?.auth?.data?.role;
    },
  },

  watch: {
    $route: {
      handler: "onRoutechange",
      immediate: true,
      deep: true,
    },
  },
  mounted() {
    this.initActiveMenu();
    document.documentElement.setAttribute("data-sidebar-user-show", true);
    if (this.rmenu == "vertical") {
      document.documentElement.setAttribute("data-layout", "vertical");
    }
    // document.getElementById("overlay").addEventListener("click", () => {
    //     document.body.classList.remove("vertical-sidebar-enable");
    // });
    window.addEventListener("resize", () => {
      if (this.layoutType == "twocolumn") {
        var windowSize = document.documentElement.clientWidth;
        alert("test");
        if (windowSize < 767) {
          document.documentElement.setAttribute("data-layout", "vertical");
          this.rmenu = "vertical";
          localStorage.setItem("rmenu", "vertical");
        } else {
          alert("test lebih");
          document.documentElement.setAttribute("data-layout", "vertical");
          this.rmenu = "twocolumn";
          localStorage.setItem("rmenu", "twocolumn");
          setTimeout(() => {
            this.initActiveMenu();
          }, 50);
        }
      }
    });
    if (document.querySelectorAll(".navbar-nav .collapse")) {
      let collapses = document.querySelectorAll(".navbar-nav .collapse");
      collapses.forEach((collapse) => {
        // Hide sibling collapses on `show.bs.collapse`
        collapse.addEventListener("show.bs.collapse", (e) => {
          e.stopPropagation();
          let closestCollapse = collapse.parentElement.closest(".collapse");
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
                if (sibling.nodeType === 1 && sibling !== elem) {
                  siblings.push(sibling);
                }
                sibling = sibling.nextSibling;
              }
              return siblings;
            };
            let siblings = getSiblings(collapse.parentElement);
            siblings.forEach((item) => {
              if (item.childNodes.length > 2) {
                item.firstElementChild.setAttribute("aria-expanded", "false");
                item.firstElementChild.classList.remove("active");
              }
              let ids = item.querySelectorAll("*[id]");
              ids.forEach((item1) => {
                item1.classList.remove("show");
                item1.parentElement.firstChild.setAttribute(
                  "aria-expanded",
                  "false"
                );
                item1.parentElement.firstChild.classList.remove("active");
                if (item1.childNodes.length > 2) {
                  let val = item1.querySelectorAll("ul li a");
                  val.forEach((subitem) => {
                    if (subitem.hasAttribute("aria-expanded"))
                      subitem.setAttribute("aria-expanded", "false");
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

  methods: {
    onRoutechange(ele) {
      this.initActiveMenu(ele.path);
      if (document.getElementsByClassName("mm-active").length > 0) {
        const currentPosition =
          document.getElementsByClassName("mm-active")[0].offsetTop;
        if (currentPosition > 500)
          if (this.$refs.isSimplebar)
            this.$refs.isSimplebar.value.getScrollElement().scrollTop =
              currentPosition + 300;
      }
    },

    initActiveMenu() {
      const pathName = window.location.pathname;
      const ul = document.getElementById("navbar-nav");
      if (ul) {
        const items = Array.from(ul.querySelectorAll("a.nav-link"));
        let activeItems = items.filter((x) => x.classList.contains("active"));
        this.removeActivation(activeItems);
        let matchingMenuItem = items.find((x) => {
          return x.getAttribute("href") === pathName;
        });
        if (matchingMenuItem) {
          this.activateParentDropdown(matchingMenuItem);
        } else {
          var id = pathName.replace("/", "");
          if (id) document.body.classList.add("twocolumn-panel");
          this.activateIconSidebarActive(pathName);
        }
      }
    },
    removeActivation(items) {
      items.forEach((item) => {
        if (item.classList.contains("menu-link")) {
          if (!item.classList.contains("active")) {
            item.setAttribute("aria-expanded", false);
          }
          item.nextElementSibling?.classList.remove("show");
        }
        if (item.classList.contains("nav-link")) {
          if (item.nextElementSibling) {
            item.nextElementSibling?.classList.remove("show");
          }
          item.setAttribute("aria-expanded", false);
        }
        item.classList.remove("active");
      });
    },
    activateIconSidebarActive(id) {
      var menu = document.querySelector(
        "#two-column-menu .simplebar-content-wrapper a[href='" +
          id +
          "'].nav-icon"
      );
      if (menu !== null) {
        menu.classList.add("active");
      }
    },

    activateParentDropdown(item) {
      // navbar-nav menu add active
      item.classList.add("active");
      let parentCollapseDiv = item.closest(".collapse.menu-dropdown");
      if (parentCollapseDiv) {
        // to set aria expand true remaining
        parentCollapseDiv.classList.add("show");
        parentCollapseDiv.parentElement.children[0].classList.add("active");
        parentCollapseDiv.parentElement.children[0].setAttribute(
          "aria-expanded",
          "true"
        );
        if (
          parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")
        ) {
          if (
            parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")
              .previousElementSibling
          ) {
            if (
              parentCollapseDiv.parentElement
                .closest(".collapse.menu-dropdown")
                .previousElementSibling.parentElement.closest(
                  ".collapse.menu-dropdown"
                )
            ) {
              const grandparent = parentCollapseDiv.parentElement
                .closest(".collapse.menu-dropdown")
                .previousElementSibling.parentElement.closest(
                  ".collapse.menu-dropdown"
                );
              this.activateIconSidebarActive(
                "#" + grandparent.getAttribute("id")
              );
              grandparent.classList.add("show");
            }
          }
          this.activateIconSidebarActive(
            "#" +
              parentCollapseDiv.parentElement
                .closest(".collapse.menu-dropdown")
                .getAttribute("id")
          );

          parentCollapseDiv.parentElement
            .closest(".collapse")
            .classList.add("show");
          if (
            parentCollapseDiv.parentElement.closest(".collapse")
              .previousElementSibling
          )
            parentCollapseDiv.parentElement
              .closest(".collapse")
              .previousElementSibling.classList.add("active");
          return false;
        }
        this.activateIconSidebarActive(
          "#" + parentCollapseDiv.getAttribute("id")
        );
        return false;
      }
      return false;
    },
  },
};
</script>

<template>
  <BContainer fluid>
    <template v-if="layoutType === 'vertical' || layoutType === 'semibox'">
      <ul class="navbar-nav h-100" id="navbar-nav">
        <li class="menu-title">
          <span data-key="t-menu" class="mt-2"> {{ $t("t-menu") }}</span>
        </li>
        <li class="nav-item">
          <router-link class="nav-link menu-link" :to="{ name: 'Dashboard' }">
            <i class="ri-honour-line"></i>
            <span data-key="t-dashboards">{{ $t("t-dashboards") }}</span>
          </router-link>
        </li>
        <li class="menu-title">
          <i class="ri-more-fill"></i>
          <span data-key="t-kerja-comp">{{ $t("t-kerja-comp") }}</span>
        </li>
        <li class="nav-item">
          <router-link class="nav-link menu-link" to="/presensi">
            <i class="ri-user-location-fill"></i>
            <span data-key="t-presensi">{{ $t("t-presensi") }}</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link class="nav-link menu-link" :to="{ name: 'HarianKerja' }">
            <i class="ri-time-fill"></i>
            <span data-key="t-presensi">{{ $t("t-jadwalku") }}</span>
          </router-link>
        </li>
        <li class="menu-title">
          <i class="ri-more-fill"></i>
          <span data-key="t-pengajuan-comp">{{ $t("t-pengajuan-comp") }}</span>
        </li>
        <li class="nav-item">
          <router-link class="nav-link menu-link" to="/izin-ku/progress">
            <i class="ri-plane-line"></i>
            <span data-key="t-izinku">{{ $t("t-izinku") }}</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link
            class="nav-link menu-link position-relative"
            to="/lembur/list"
          >
            <i class="ri-hand-coin-line"></i>
            <span data-key="t-side-lembur">{{ $t("t-side-lembur") }}</span>
            <BBadge v-if="$store.state.menu.menu.lembur > 0" variant="danger">{{
              $store.state.menu.menu.lembur
            }}</BBadge>
          </router-link>
        </li>
        <li v-if="['SUPER_ADMIN'].includes(myRole)" class="nav-item">
          <BLink
            class="nav-link menu-link"
            href="#sidebarJadwal"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarSetting"
          >
            <i class="ri-calendar-fill"></i>
            <span data-key="t-jadwal"> {{ $t("t-jadwal") }}</span>
          </BLink>
          <div class="collapse menu-dropdown" id="sidebarJadwal">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item">
                <router-link class="nav-link menu-link" to="/harian-jadwal">
                  <!-- <i class="ri-calendar-check-fill"></i> -->
                  <span data-key="t-jadwal-ku">{{ $t("t-jadwal-ku") }}</span>
                </router-link>
              </li>
              <li class="nav-item">
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
        <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
          <BLink
            class="nav-link menu-link"
            href="#sidebarMaster"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarMaster"
          >
            <i class="ri-dashboard-2-line"></i>
            <span data-key="t-master"> {{ $t("t-master") }}</span>
          </BLink>
          <div class="collapse menu-dropdown" id="sidebarMaster">
            <ul class="nav nav-sm flex-column">
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-karyawan"
                  class="nav-link custom-abc"
                  data-key="t-karyawan"
                >
                  {{ $t("t-karyawan") }}
                </router-link>
              </li>
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-shift"
                  class="nav-link custom-abc"
                  data-key="t-shift"
                >
                  {{ $t("t-shift") }}
                </router-link>
              </li>
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-izin"
                  class="nav-link custom-abc"
                  data-key="t-izin"
                >
                  {{ $t("t-izin") }}
                </router-link>
              </li>
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-lembur"
                  class="nav-link custom-abc"
                  data-key="t-m-lembur"
                >
                  {{ $t("t-m-lembur") }}
                </router-link>
              </li>
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-unit"
                  class="nav-link custom-abc"
                  data-key="t-unit"
                >
                  {{ $t("t-unit") }}
                </router-link>
              </li>
            </ul>
          </div>
        </li>
        <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
          <BLink
            class="nav-link menu-link"
            href="#sidebarSetting"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarSetting"
          >
            <i class="ri-settings-fill"></i>
            <span data-key="t-setting"> {{ $t("t-setting") }}</span>
          </BLink>
          <div class="collapse menu-dropdown" id="sidebarSetting">
            <ul class="nav nav-sm flex-column">
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  to="/m-lokasi"
                  class="nav-link custom-abc"
                  data-key="t-analytics"
                >
                  {{ $t("t-lokasi") }}
                </router-link>
              </li>
              <li v-if="['SUPER_ADMIN'].includes(this.myRole)" class="nav-item">
                <router-link
                  :to="{ name: 'Companies' }"
                  class="nav-link custom-abc"
                  data-key="t-analytics"
                >
                  {{ $t("t-companies") }}
                </router-link>
              </li>
            </ul>
          </div>
        </li>
        <li class="menu-title">
          <i class="ri-more-fill"></i>
          <span data-key="t-riwayat-comp">{{ $t("t-riwayat-comp") }}</span>
        </li>
        <li class="nav-item">
          <BLink
            class="nav-link menu-link"
            href="#sidebarHistory"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarHistory"
          >
            <i class="ri-history-fill"></i>
            <span data-key="t-history"> {{ $t("t-history") }}</span>
          </BLink>
          <div class="collapse menu-dropdown" id="sidebarHistory">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item">
                <router-link
                  to="/history/presensi/list"
                  class="nav-link custom-abc"
                  data-key="t-history-presensi"
                >
                  {{ $t("t-history-presensi") }}
                </router-link>
              </li>
              <li class="nav-item">
                <router-link
                  to="/history/izin"
                  class="nav-link custom-abc"
                  data-key="t-history-izin"
                >
                  {{ $t("t-history-izin") }}
                </router-link>
              </li>
              <li class="nav-item">
                <router-link
                  to="/history/lembur"
                  class="nav-link custom-abc"
                  data-key="t-history-lembur"
                >
                  {{ $t("t-history-lembur") }}
                </router-link>
              </li>
            </ul>
          </div>
        </li>
        <li
          v-if="
            ['KEPALA', 'KASUB', 'KABID', 'SUPER_ADMIN', 'DIREKTUR'].includes(
              this.myRole
            )
          "
          class="menu-title"
        >
          <i class="ri-more-fill"></i>
          <span data-key="t-rekap-comp">{{ $t("t-rekap-comp") }}</span>
        </li>
        <li
          v-if="
            ['KEPALA', 'KASUB', 'KABID', 'SUPER_ADMIN', 'DIREKTUR'].includes(
              this.myRole
            )
          "
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
            <i class="ri-git-repository-commits-fill"></i>
            <span data-key="t-rekap"> {{ $t("t-rekap") }}</span>
          </BLink>
          <div class="collapse menu-dropdown" id="sidebarRekap">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item">
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
    </template>
  </BContainer>
</template>
