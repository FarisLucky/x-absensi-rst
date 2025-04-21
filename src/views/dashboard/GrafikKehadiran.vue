<template>
  <div class="attendance-dashboard">
    <BRow class="g-2">
      <BCol md="7">
        <BCard class="mb-0">
          <div class="d-flex justify-content-between align-items-center">
            <MonthPickerInput
              lang="id"
              @input="onFilterDivisi"
              placeholder="Pilih Bulan"
              :clearable="true"
              :default-month="new Date().getMonth() + 1"
              :default-year="new Date().getFullYear()"
            ></MonthPickerInput>
            <h4>Kehadiran per Divisi</h4>
          </div>
          <v-chart class="chart" :option="barChartOption" autoresize />
        </BCard>
      </BCol>
      <BCol md="5">
        <BCard class="mb-0">
          <div class="d-flex justify-content-between align-items-center">
            <MonthPickerInput
              lang="id"
              v-model="filter.distr"
              @input="onFilterDistr"
              placeholder="Pilih Bulan"
              :clearable="true"
              :default-month="new Date().getMonth() + 1"
              :default-year="new Date().getFullYear()"
            ></MonthPickerInput>
            <h4>Distribusi Kehadiran</h4>
          </div>
          <v-chart class="chart" :option="pieChartOption" autoresize />
        </BCard>
      </BCol>
      <BCol md="7">
        <BCard class="mb-0">
          <h4>Trend 7 Hari Terakhir</h4>
          <v-chart class="chart" :option="lineChartOption" autoresize />
        </BCard>
      </BCol>
      <BCol md="5">
        <BCard class="mb-0">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <MonthPickerInput
              lang="id"
              v-model="filter.hadir"
              @input="onFilterKehadiran"
              placeholder="Pilih Bulan"
              :clearable="true"
              :default-month="new Date().getMonth() + 1"
              :default-year="new Date().getFullYear()"
            ></MonthPickerInput>
            <h4>Statistik Kehadiran</h4>
          </div>
          <BRow class="g-2">
            <BCol cols="6" v-for="stat in summaryStats" :key="stat.title">
              <BCard :style="{ color: stat.color }">
                <h5>
                  {{ stat.value }}
                </h5>
                <BCardFooter>{{ stat.title }}</BCardFooter>
              </BCard>
            </BCol>
          </BRow>
        </BCard>
      </BCol>
    </BRow>
  </div>
</template>

<script>
import { use } from "echarts/core";
import { PieChart, BarChart, LineChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";
import { ref, onMounted, computed } from "vue";
import dayjs from "dayjs";
import queryString from "query-string";
import { dashboardService } from "@/services/DashboardService";
import { MonthPickerInput } from "vue-month-picker";
import store from "@/state/store";

use([
  PieChart,
  BarChart,
  LineChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
  CanvasRenderer,
]);

export default {
  components: { VChart, MonthPickerInput },
  setup() {
    const selectedDate = ref(dayjs().format("YYYY-MM-DD"));
    const attendanceData = ref({
      hadir: {},
      distr: {},
      byDepartment: [],
      trend: [],
    });
    let filter = ref({
      hadir: new Date(),
      distr: new Date(),
      divisi: new Date(),
    });

    const onFilterDivisi = (e) => {
      filter.value.divisi = new Date(e.year, e.monthIndex - 1, 1);
      fetchDataKehadiranDivisi();
    };
    const onFilterDistr = (e) => {
      filter.value.distr = new Date(e.year, e.monthIndex - 1, 1);
      fetchDataKehadiranDistr();
    };
    const onFilterKehadiran = (e) => {
      filter.value.hadir = new Date(e.year, e.monthIndex - 1, 1);
      fetchDataKehadiran();
    };
    const onFilterTrend = (e) => {
      filter.value.trend = new Date(e.year, e.monthIndex - 1, 1);
      fetchDataKehadiranTrend();
    };

    const fetchDataKehadiran = async () => {
      try {
        let params = {
          hadir: `${filter.value.distr.getFullYear()}-${
            filter.value.distr.getMonth() + 1
          }`,
        };
        const query = queryString.stringify(Object.assign({}, params), {
          arrayFormat: "index",
        });
        const [err, resp] = await dashboardService.statistikKehadiran(query);
        if (err) {
          store.dispatch("toast/toastError", {
            title: "Gagal",
            msg: err.response?.data?.errors,
          });

          return;
        }

        attendanceData.value.hadir = resp.data;
      } catch (error) {
        console.error("Error fetching attendance data:", error);
      }
    };

    const fetchDataKehadiranDivisi = async () => {
      try {
        let params = {
          divisi: `${filter.value.divisi.getFullYear()}-${
            filter.value.divisi.getMonth() + 1
          }`,
        };
        const query = queryString.stringify(Object.assign({}, params), {
          arrayFormat: "index",
        });
        const [err, resp] = await dashboardService.statistikKehadiranDivisi(
          query
        );
        if (err) {
          store.dispatch("toast/toastError", {
            title: "Gagal",
            msg: err.response?.data?.errors,
          });

          return;
        }
        // console.log(resp.data);
        let val = resp.data;
        attendanceData.value.byDepartment = {
          unit: val.unit,
          present: val.present,
          permission: val.permission,
          absent: val.absent,
        };
      } catch (error) {
        console.error("Error fetching attendance data:", error);
      }
    };

    const fetchDataKehadiranDistr = async () => {
      try {
        let params = {
          distr: `${filter.value.distr.getFullYear()}-${
            filter.value.distr.getMonth() + 1
          }`,
        };
        const query = queryString.stringify(Object.assign({}, params), {
          arrayFormat: "index",
        });
        const [err, resp] = await dashboardService.statistikKehadiranDistr(
          query
        );
        if (err) {
          store.dispatch("toast/toastError", {
            title: "Gagal",
            msg: err.response?.data?.errors,
          });

          return;
        }

        attendanceData.value.distr = resp.data;
      } catch (error) {
        console.error("Error fetching attendance data:", error);
      }
    };

    const fetchDataKehadiranTrend = async () => {
      try {
        const [err, resp] = await dashboardService.statistikKehadiranTrend();
        if (err) {
          store.dispatch("toast/toastError", {
            title: "Gagal",
            msg: err.response?.data?.errors,
          });

          return;
        }

        attendanceData.value.trend = resp.data;
      } catch (error) {
        console.error("Error fetching attendance data:", error);
      }
    };

    // Summary statistics cards
    const summaryStats = computed(() => [
      {
        title: "Total Karyawan",
        value: attendanceData.value.hadir.total_employees || 0,
        color: "#333",
      },
      {
        title: "Hadir",
        value: attendanceData.value.hadir.present || 0,
        color: "#28a745",
      },
      {
        title: "Izin",
        value: attendanceData.value.hadir.permission || 0,
        color: "#ffc107",
      },
      {
        title: "Alfa",
        value: attendanceData.value.hadir.absent || 0,
        color: "#dc3545",
      },
      {
        title: "Persentase Hadir",
        value: attendanceData.value.hadir.attendance_rate
          ? `${attendanceData.value.hadir.attendance_rate}%`
          : "0%",
        color: "#17a2b8",
      },
    ]);

    // Pie Chart Options
    const pieChartOption = computed(() => ({
      tooltip: { trigger: "item", formatter: "{a} <br/>{b}: {c} ({d}%)" },
      legend: { orient: "vertical", left: "left" },
      series: [
        {
          name: "Status Kehadiran",
          type: "pie",
          radius: ["40%", "70%"],
          avoidLabelOverlap: false,
          itemStyle: {
            borderRadius: 10,
            borderColor: "#fff",
            borderWidth: 2,
          },
          label: { show: true, formatter: "{b}: {c} ({d}%)" },
          emphasis: {
            label: {
              show: true,
              fontSize: "18",
              fontWeight: "bold",
            },
          },
          data: [
            {
              value: attendanceData.value.distr.present || 0,
              name: "Hadir",
            },
            {
              value: attendanceData.value.distr.permission || 0,
              name: "Izin",
            },
            {
              value: attendanceData.value.distr.absent || 0,
              name: "Alfa",
            },
          ],
        },
      ],
    }));

    // Bar Chart Options
    const barChartOption = computed(() => ({
      tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
      legend: { data: ["Hadir", "Izin", "Alfa"], bottom: 0 },
      grid: {
        left: "3%",
        right: "4%",
        bottom: "15%",
        containLabel: true,
      },
      xAxis: {
        type: "category",
        data: attendanceData.value.byDepartment.unit,
      },
      yAxis: { type: "value" },
      series: [
        {
          name: "Hadir",
          type: "bar",
          stack: "total",
          data: attendanceData.value.byDepartment.present,
        },
        {
          name: "Izin",
          type: "bar",
          stack: "total",
          data: attendanceData.value.byDepartment.permission,
        },
        {
          name: "Alfa",
          type: "bar",
          stack: "total",
          data: attendanceData.value.byDepartment.absent,
        },
      ],
    }));

    // Line Chart Options
    const lineChartOption = computed(() => ({
      tooltip: { trigger: "axis" },
      legend: { data: ["Hadir", "Izin", "Alfa"], bottom: 0 },
      grid: {
        left: "3%",
        right: "4%",
        bottom: "15%",
        containLabel: true,
      },
      xAxis: {
        type: "category",
        data: attendanceData.value.trend.date,
      },
      yAxis: { type: "value" },
      series: [
        {
          name: "Hadir",
          type: "line",
          data: attendanceData.value.trend.present,
        },
        {
          name: "Izin",
          type: "line",
          data: attendanceData.value.trend.permission,
        },
        {
          name: "Alfa",
          type: "line",
          data: attendanceData.value.trend.absent,
        },
      ],
    }));

    onMounted(() => {
      fetchDataKehadiran();
      fetchDataKehadiranDivisi();
      fetchDataKehadiranDistr();
      fetchDataKehadiranTrend();
    });

    return {
      selectedDate,
      attendanceData,
      summaryStats,
      pieChartOption,
      barChartOption,
      lineChartOption,
      filter,
      onFilterDistr,
      onFilterDivisi,
      onFilterKehadiran,
      onFilterTrend,
    };
  },
};
</script>

<style scoped>
.chart {
  height: 340px;
  width: 100%;
}
</style>
