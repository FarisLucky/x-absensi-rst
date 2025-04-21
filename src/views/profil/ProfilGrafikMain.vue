<template lang="">
  <BCard no-body class="card-animate overflow-hidden mb-1">
    <div class="position-absolute start-0 widget-pattern" style="z-index: 0">
      <svg
        version="1.2"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 200 120"
        width="200"
        height="120"
      >
        <path
          id="Shape 8"
          class="s0"
          d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"
        />
      </svg>
    </div>
    <BCardBody style="z-index: 1" class="p-2">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1 overflow-hidden">
          <p class="text-uppercase fw-medium text-muted text-truncate mb-3">
            {{ statistikData.label }}
          </p>
          <h4 class="fs-16 fw-semibold ff-secondary mb-0">
            <count-to
              :startVal="0"
              :endVal="statistikData?.val"
              :duration="5000"
            ></count-to>
          </h4>
        </div>
        <div class="flex-shrink-0">
          <v-chart
            class="chart"
            :option="option"
            autoresize
            style="width: 100%"
          />
        </div>
      </div>
    </BCardBody>
  </BCard>
</template>
<script>
import { CountTo } from "vue3-count-to";
import { layoutComputed } from "@/state/helpers.js";

import { use } from "echarts/core";
import {
  TitleComponent,
  PolarComponent,
  TooltipComponent,
} from "echarts/components";
import { BarChart } from "echarts/charts";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";

export default {
  setup() {
    use([
      TitleComponent,
      TooltipComponent,
      CanvasRenderer,
      PolarComponent,
      BarChart,
    ]);
  },
  components: {
    CountTo,
    VChart,
  },
  props: ["statistikData", "label"],
  data() {
    return {
      option: {
        polar: {
          radius: [18, "100%"],
        },
        angleAxis: {
          show: false,
          max: 100,
          startAngle: 90,
        },
        radiusAxis: {
          type: "category",
          data: ["a"],
        },
        series: {
          type: "bar",
          data: [this.statistikData.percent],
          coordinateSystem: "polar",
          label: {
            show: true,
            position: "middle",
            formatter: "{c} %",
          },
        },
      },
    };
  },
  computed: {
    ...layoutComputed,
    layoutTheme() {
      return this.$store ? this.$store.state.layout.layoutTheme : {} || {};
    },
  },
  mounted() {
    // this.handleUpdate();
    console.log(this.statistikData.percent);
  },
  watch: {
    layoutTheme() {
      this.handleUpdate();
    },
  },
};
</script>
<style scoped>
.chart {
  min-height: 90px;
}
</style>
