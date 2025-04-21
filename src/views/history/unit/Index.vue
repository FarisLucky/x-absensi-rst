<template>
    <BCard no-body>
        <BCardBody>
            <p class="text-muted">History Unit berdasarkan data sistem.</p>
            <BTabs
                nav-class="nav-border-top nav-border-top-primary mb-3"
                justified
            >
                <BTab
                    v-for="(tab, idx) in list"
                    :active="active === tab.route"
                    :key="idx"
                >
                    <template #title>
                        <i class="align-middle me-1" :class="tab.icon"></i>
                        {{ tab.title }}
                    </template>
                    <component :is="tab.component" />
                </BTab>
            </BTabs>
        </BCardBody>
    </BCard>
</template>
<script>
import { defineAsyncComponent } from "vue";

export default {
    components: {
        List: defineAsyncComponent(() => import("./List")),
    },
    data() {
        return {
            list: [
                {
                    title: "Presensi",
                    component: "Presensi",
                    route: "Presensi",
                    icon: "ri-task-fill",
                },
            ],
            active: "Presensi",
        };
    },
};
</script>
