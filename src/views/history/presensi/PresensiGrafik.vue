<template>
  <div>
    <BRow class="g-2">
      <BCol lg="2">
        <div class="search-box">
          <input
            type="text"
            class="form-control search bg-light border-light"
            placeholder="Cari Karyawan disini..."
            v-model="filter.search"
          />
          <i class="ri-search-line search-icon"></i>
        </div>
      </BCol>
      <BCol lg="2">
        <v-select
          v-model="filter.year"
          :options="years"
          placeholder="Pilih Tahun"
        ></v-select>
      </BCol>
      <BCol v-if="filter.year > 0" lg="2">
        <v-select
          v-model="filter.month"
          :options="months"
          :reduce="(l) => l.id"
          label="name"
          placeholder="Pilih Bulan"
          @option:selected="fetchData"
        ></v-select>
      </BCol>
      <BCol
        v-if="filter.month > 0 && (isSuperAdmin || isKaBid || isKaSub || isDir)"
        lg="3"
      >
        <v-select
          v-model="filter.unit"
          :options="units"
          :reduce="(l) => l.id"
          label="nama"
          placeholder="Pilih Unit"
          @option:selected="fetchData"
        ></v-select>
      </BCol>
      <BCol>
        <BButton type="button" variant="outline-secondary" @click="onRefresh">
          <i class="ri-refresh-fill me-1 align-bottom"></i>
          Reset
        </BButton>
      </BCol>
      <BCol cols="12">
        <div class="border rounded p-1">
          <v-chart class="chart" :option="option" autoresize />
        </div>
      </BCol>
    </BRow>
  </div>
</template>

<script>
import { registerTheme, use } from "echarts/core";
import {
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
} from "echarts/components";
import { BarChart } from "echarts/charts";
import { CanvasRenderer } from "echarts/renderers";
import VChart, { THEME_KEY } from "vue-echarts";
import { spinnerMethods, toastMethods } from "@/state/helpers";
import { historyPresensiService } from "@/services/HistoryPresensiService";
import { mUnitService } from "@/services/MUnitService";
import queryString from "query-string";
import macarons from "@/assets/echarts/theme1.json";
import {
  DIREKTUR,
  getYears,
  KABID,
  KASUB,
  months,
  SUPER_ADMIN,
} from "@/helpers/utils";
import { provide } from "vue";

const initFilter = () => {
  const currentTime = new Date();

  return {
    search: "",
    month: currentTime.getMonth() + 1,
    year: currentTime.getFullYear(),
    unit: "",
  };
};

export default {
  setup() {
    use([
      TitleComponent,
      TooltipComponent,
      GridComponent,
      LegendComponent,
      BarChart,
      CanvasRenderer,
    ]);
    registerTheme("myCustomTheme", macarons);
    provide(THEME_KEY, "myCustomTheme");
  },
  components: {
    VChart,
  },
  data() {
    return {
      filter: initFilter(),
      option: {
        title: {
          text: "Presentase Kehadiran",
        },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "shadow",
          },
        },
        legend: {},
        grid: {
          left: "3%",
          right: "4%",
          bottom: "3%",
          containLabel: true,
        },
        toolbox: {
          show: true,
          feature: {
            saveAsImage: {},
          },
        },
        xAxis: {
          name: "Presentase",
          type: "value",
          min: 0,
          max: 100,
          // boundaryGap: [0, 0.01],
          axisLabel: {
            formatter: "{value} %",
          },
        },
        yAxis: {
          type: "category",
          axisLabel: {
            formatter: "{value} %",
          },
          data: [],
        },
        series: [],
      },
      tanggal: null,
      months,
      years: [],
      units: [],
      lists: [],
    };
  },

  created() {
    this.years = getYears();
    this.onRefresh();
  },
  computed: {
    isSuperAdmin() {
      return this.$store.state.auth.data.role === SUPER_ADMIN;
    },
    isKaSub() {
      return this.$store.state.auth.data.role === KASUB;
    },
    isKaBid() {
      return this.$store.state.auth.data.role === KABID;
    },
    isDir() {
      return this.$store.state.auth.data.role === DIREKTUR;
    },
  },
  methods: {
    ...spinnerMethods,
    ...toastMethods,
    async fetchData() {
      this.show();

      let query = queryString.stringify(Object.assign(this.filter), {
        arrayFormat: "index",
      });

      const [err, resp] = await historyPresensiService.grafikKehadiran(query);
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.lists = resp.data.rows;
      this.option.yAxis.data = resp.data.labels;
      this.option.series = Object.entries(resp.data.series).map((data) => {
        return {
          name: data[0],
          type: "bar",
          label: {
            show: true,
            formatter: "{c} %",
          },
          data: data[1],
        };
      });
      this.hide();
    },
    onRefresh() {
      this.fetchData();
      this.getUnit();
    },
    async getUnit() {
      this.show();
      const [err, resp] = await mUnitService.all();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }
      this.units = resp.data;
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
