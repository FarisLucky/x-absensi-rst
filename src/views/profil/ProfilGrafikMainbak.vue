<template>
  <v-chart
    class="chart"
    :option="option"
    autoresize
    style="width: 100%; height: 340px"
  />
</template>

<script>
import { use } from "echarts/core";
import {
  TitleComponent,
  ToolboxComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
} from "echarts/components";
import { LineChart } from "echarts/charts";
import { UniversalTransition } from "echarts/features";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";
import { dashboardService } from "@/services/DashboardService";
import { spinnerMethods, toastMethods } from "@/state/helpers";

export default {
  setup() {
    use([
      TitleComponent,
      ToolboxComponent,
      TooltipComponent,
      GridComponent,
      LegendComponent,
      LineChart,
      CanvasRenderer,
      UniversalTransition,
    ]);
  },
  components: {
    VChart,
  },
  data() {
    return {
      lists: [],
      option: {
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "cross",
            label: {
              backgroundColor: "#6a7985",
            },
          },
        },
        xAxis: {
          type: "category",
          data: [],
        },
        yAxis: {
          type: "value",
        },
        series: [],
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    async fetchData() {
      this.show();
      const [err, resp] = await dashboardService.presensiHarian();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.option.xAxis.data = resp.data.label;
      this.option.series.push({
        data: resp.data.series,
        type: "line",
      });
      this.hide();
    },
  },
};
</script>

<style scoped>
.chart {
  height: 100vh;
}
</style>
