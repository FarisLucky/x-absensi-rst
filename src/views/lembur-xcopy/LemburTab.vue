<script>
import { defineAsyncComponent } from "vue";
import List from "./List";

export default {
    components: {
        List,
        ProgressList: defineAsyncComponent(() => import("./ProgressList")),
        ConfirmList: defineAsyncComponent(() => import("./ConfirmList")),
    },
    data() {
        return {
            list: [
                {
                    title: "Progress",
                    component: "ProgressList",
                    route: "progress",
                    icon: "ri-restart-fill",
                },
                {
                    title: "Konfirmasi",
                    component: "ConfirmList",
                    route: "confirm",
                    icon: "ri-checkbox-multiple-fill",
                },
                {
                    title: "Absen",
                    component: "List",
                    route: "absen",
                    icon: "ri-task-fill",
                },
            ],
        };
    },
    computed: {
        currentTab() {
            let current = this.list.find(
                (item) => item.route === this.$route.params.jenis
            );

            return current ? current.component : null;
        },
    },
    methods: {
        onChangeTab(params) {
            this.$router.push({
                name: "PengajuanLembur",
                params: { jenis: params },
            });
        },
    },
};
</script>

<template>
    <BCard no-body>
        <BCardBody class="bg-cust">
            <div class="d-flex justify-content-between mb-1">
                <h5 class="fs-14 d-inline-block">
                    <i class="ri-exchange-fill"></i>
                    Pengajuan <strong class="text-primary">lembur</strong> pada
                    unit masing-masing
                </h5>
            </div>
            <BTabs nav-class="nav-border-top nav-border-top-primary mb-3">
                <BTab
                    v-for="(tab, idx) in list"
                    :key="idx"
                    :active="$route.params.jenis === tab.route"
                    lazy
                    @click.prevent="onChangeTab(tab.route)"
                >
                    <template #title>
                        <i class="align-middle me-1" :class="tab.icon"></i>
                        {{ tab.title }}
                    </template>
                    <component :is="currentTab" />
                </BTab>
            </BTabs>
        </BCardBody>
    </BCard>
</template>
