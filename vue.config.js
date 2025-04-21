const { defineConfig } = require("@vue/cli-service");
const webpack = require("webpack");

module.exports = defineConfig({
  //   transpileDependencies: ["@vueform"],
  publicPath: "/",
  devServer: {
    client: {
      overlay: {
        runtimeErrors: (error) => {
          const ignoreErrors = [
            "ResizeObserver loop limit exceeded",
            "ResizeObserver loop completed with undelivered notifications.",
          ];
          if (ignoreErrors.includes(error.message)) {
            return false;
          }
          return true;
        },
      },
    },
  },
  productionSourceMap: false,
  configureWebpack: {
    plugins: [
      new webpack.DefinePlugin({
        // Vue CLI is in maintenance mode, and probably won't merge my PR to fix this in their tooling
        // https://github.com/vuejs/vue-cli/pull/7443
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: "false",
      }),
    ],
    optimization: {
      splitChunks: {
        chunks: "all",
      },
    },
  },
  pwa: {
    appleMobileWebAppCapable: "yes",
    appleMobileWebAppStatusBarStyle: "#00695C",
    // swSrc: "service-worker.js",
    // workboxPluginMode: "InjectManifest",
    // workboxOptions: {
    //   // swSrc is required in InjectManifest mode.
    //   swSrc: "./src/config/firebase-msg-sw.js",
    //   // ...other Workbox options...
    // },
    name: "Timesheet CKI",
    themeColor: "#42b983",
    msTileColor: "#42b983",
    manifestOptions: {
      background_color: "#ffffff",
    },
    workboxOptions: {
      skipWaiting: true,
    },
    workboxOptions: {
      runtimeCaching: [
        {
          urlPattern: /^https:\/\/timesheet\.co.id\/.*$/, // Match your API requests
          handler: "NetworkFirst",
          options: {
            cacheName: "api-cache",
            expiration: {
              maxAgeSeconds: 60 * 60 * 24, // 1 day
            },
          },
        },
        {
          urlPattern: /\.(?:png|jpg|jpeg|svg|gif)$/, // Use 'CacheFirst' for images
          handler: "CacheFirst",
          options: {
            cacheName: "image-cache",
            expiration: {
              maxEntries: 50,
              maxAgeSeconds: 30 * 24 * 60 * 60, // 30 days
            },
          },
        },
        {
          urlPattern: /\.css$/,
          handler: "StaleWhileRevalidate", // Use 'StaleWhileRevalidate' for styles
          options: {
            cacheName: "css-cache",
          },
        },
        {
          urlPattern: /\.js$/,
          handler: "StaleWhileRevalidate", // Use 'StaleWhileRevalidate' for scripts
          options: {
            cacheName: "js-cache",
          },
        },
      ],
    },
  },
});
