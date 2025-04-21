<template>
  <v-chart
    class="chart"
    :option="option"
    autoresize
    :loading="loading"
    style="max-height: 100%"
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
import { LineChart, BarChart } from "echarts/charts";
import { UniversalTransition } from "echarts/features";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { profileService } from "@/services/ProfileService";

export default {
  setup() {
    use([
      TitleComponent,
      ToolboxComponent,
      TooltipComponent,
      GridComponent,
      LegendComponent,
      LineChart,
      BarChart,
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
        title: {
          show: true,
          text: "Grafik Kehadiran Perbulan",
        },
        legend: {
          data: ["Hadir", "Izin"],
        },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "cross",
            label: {
              backgroundColor: "#6a7985",
            },
          },
        },
        toolbox: {
          feature: {
            saveAsImage: { show: true },
          },
        },
        xAxis: {
          type: "category",
          data: [],
          axisPointer: {
            type: "shadow",
          },
        },
        yAxis: {
          type: "value",
          min: 0,
          axisLabel: {
            show: true,
            formatter: "{value} hari",
          },
        },
        series: [],
      },
      loading: false,
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
      this.loading = true;
      const [err, resp] = await profileService.statistikPresensi(
        this.$route.params.nip
      );
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();
        this.loading = false;

        return;
      }
      this.option.xAxis.data = resp.data.label;
      this.option.series.push({
        name: "Hadir",
        data: resp.data.series.presensi,
        type: "line",
        tooltip: {
          valueFormatter: function (value) {
            return value + " hari";
          },
        },
        stack: "Total",
        label: {
          show: true,
          formatter: "{c} hari",
        },
      });
      this.option.series.push({
        name: "Izin",
        data: resp.data.series.izin,
        type: "line",
        tooltip: {
          valueFormatter: function (value) {
            return value + " hari";
          },
        },
        stack: "Total",
        label: {
          show: true,
          formatter: "{c} hari",
        },
      });
      this.hide();
      this.loading = false;
    },
  },
};
</script>

<style scoped>
.chart {
  height: 100vh;
}
</style>
