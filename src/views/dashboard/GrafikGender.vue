<template>
  <BCard>
    <div class="gender-chart-container">
      <v-chart class="chart" :option="chartOption" :autoresize="true" />
      <div class="chart-legend">
        <div v-for="item in results" :key="item.name" class="legend-item">
          <span
            class="legend-color"
            :style="{ backgroundColor: item.itemStyle.color }"
          ></span>
          <span class="legend-label">
            {{ item.name }}: {{ item.value }} ({{
              calculatePercentage(item.value)
            }}%)
          </span>
        </div>
      </div>
    </div>
  </BCard>
</template>

<script>
import { use } from "echarts/core";
import { PieChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";
import { dashboardService } from "@/services/DashboardService";
import store from "@/state/store";

use([
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  CanvasRenderer,
]);

export default {
  components: { VChart },
  data() {
    return {
      results: [
        {
          value: 0,
          name: "Laki-laki",
          id: "l",
          itemStyle: { color: "#5470C6" },
        },
        {
          value: 0,
          name: "Perempuan",
          id: "p",
          itemStyle: { color: "#EE6666" },
        },
      ],
      ttl: 0,
    };
  },
  computed: {
    chartOption() {
      return {
        title: {
          text: "Perbandingan Gender",
          left: "left",
          top: 20,
        },
        tooltip: {
          trigger: "item",
          formatter: "{a} <br/>{b}: {c} ({d}%)",
        },
        series: [
          {
            name: "Jumlah",
            type: "pie",
            radius: ["40%", "70%"],
            avoidLabelOverlap: false,
            itemStyle: {
              borderRadius: 10,
              borderColor: "#fff",
              borderWidth: 2,
            },
            label: {
              show: true,
              formatter: "{b}: {c} ({d}%)",
            },
            emphasis: {
              label: {
                show: true,
                fontSize: "18",
                fontWeight: "bold",
              },
            },
            labelLine: {
              show: true,
            },
            data: this.results,
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
      };
    },
  },
  mounted() {
    this.fetchDataGender();
    window.addEventListener("resize", this.handleResize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
  },
  methods: {
    calculatePercentage(value) {
      return this.ttl > 0 ? ((value / this.ttl) * 100).toFixed(1) : 0;
    },
    handleResize() {
      if (this.chart) {
        this.chart.resize();
      }
    },

    async fetchDataGender() {
      try {
        const [err, resp] = await dashboardService.statistikGender();
        if (err) {
          store.dispatch("toast/toastError", {
            title: "Gagal",
            msg: err.response?.data?.errors,
          });

          return;
        }

        this.results[0].value = resp.data.l;
        this.results[1].value = resp.data.p;
        this.ttl += resp.data.l;
        this.ttl += resp.data.p;
      } catch (error) {
        console.error("Error fetching attendance data:", error);
      }
    },
  },
};
</script>

<style scoped>
.gender-chart-container {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.chart {
  width: 100%;
  height: 200px;
}

.chart-legend {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.legend-color {
  display: inline-block;
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-label {
  font-size: 12spx;
}
</style>
