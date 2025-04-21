
<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://example.com)

Aplikasi ABSENSI berbasis GPS

Here's why:
* Absen mudah berdasarkan GPS dengan radius
* Rekap Laporan Harian, Bulanan, Tahunan dapat di download secara realtim
  
<!-- GETTING STARTED -->
## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

# Apliksa Absenku Berbasis Mobile Menggunakan Laravel, Vue JS dan Flutter

Presensi berdasarkan radius titik koordinat, Penjadwalan, Perizinan, Tukar Shift, Data karyawan

Fitur Belum Selesai:

1. Laporan & Grafik TurnOver Pegawai
   a. Line Chart: Tren tingkat turnover pegawai dari tahun ke tahun.
   b. Bar Chart: Perbandingan jumlah pegawai masuk vs keluar.

# CONFIG VUE JS

```sh
const path = require("path");
const CompressionPlugin = require("compression-webpack-plugin");
const PreloadPlugin = require("@vue/preload-webpack-plugin");

const myCompressionPlug = new CompressionPlugin({
  algorithm: "gzip",
  test: /\.js$|\.css$|\.png$|\.svg$|\.jpg$|\.woff2$/i,
  deleteOriginalAssets: false,
});

const myPreloadPlug = new PreloadPlugin({
  rel: "preload",
  as(entry) {
    if (/\.css$/.test(entry)) return "style";
    if (/\.woff2$/.test(entry)) return "font";
    return "script";
  },
  include: "allAssets",
  fileWhitelist: [
    /\.woff2(\?.*)?$/i,
    /\/(vue|vendor~app|chunk-common|bootstrap~app|apollo|app|home|project)\./,
  ],
});

module.exports = {
  productionSourceMap: process.env.NODE_ENV !== "production",
  chainWebpack: (config) => {
    config.plugins.delete("prefetch");
    config.plugin("CompressionPlugin").use(myCompressionPlug);
    const types = ["vue-modules", "vue", "normal-modules", "normal"];
    types.forEach((type) =>
      addStyleResource(config.module.rule("stylus").oneOf(type))
    );
  },
  configureWebpack: {
    plugins: [myPreloadPlug],
    optimization: {
      splitChunks: {
        cacheGroups: {
          default: false,
          vendors: false,
          vue: {
            chunks: "all",
            test: /[\\/]node_modules[\\/]((vue).*)[\\/]/,
            priority: 20,
          },
          bootstrap: {
            chunks: "all",
            test: /[\\/]node_modules[\\/]((bootstrap).*)[\\/]/,
            priority: 20,
          },
          apollo: {
            chunks: "all",
            test: /[\\/]node_modules[\\/]((apollo).*)[\\/]/,
            priority: 20,
          },
          vendor: {
            chunks: "all",
            test: /[\\/]node_modules[\\/]((?!(vue|bootstrap|apollo)).*)[\\/]/,
            priority: 20,
          },
          // common chunk
          common: {
            test: /[\\/]src[\\/]/,
            minChunks: 2,
            chunks: "all",
            priority: 10,
            reuseExistingChunk: true,
            enforce: true,
          },
        },
      },
    },
  },
};

function addStyleResource(rule) {
  rule
    .use("style-resource")
    .loader("style-resources-loader")
    .options({
      patterns: [path.resolve(__dirname, "./src/styles/sass/*.scss")],
    });
}
```
