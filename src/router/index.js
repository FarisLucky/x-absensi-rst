import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";
import appConfig from "../../app.config";
import store from "@/state/store";

const router = createRouter({
  history: createWebHistory("/"),
  routes,
  mode: "history",
  scrollBehavior(to) {
    if (to.hash) {
      return {
        el: to.hash,
        behavior: "smooth",
      };
    }
    return { top: 0, behavior: "smooth" };
  },
  //   scrollBehavior(savedPosition) {
  //     if (savedPosition) {
  //       return savedPosition;
  //     } else {
  //       return { top: 0, left: 0 };
  //     }
  //   },
});

router.beforeEach(async (routeTo, routeFrom, next) => {
  const publicPages = [
    "/login",
    "/register",
    "/forgot-password",
    "/kode-red-blue",
  ];
  const authpage = !publicPages.includes(routeTo.path);
  const authRequired = routeTo.matched.some((route) => route.meta.authRequired);

  /* GET ACTION VUEX STORE */
  let authStore = await store.dispatch("auth/getLogin");
  const loggeduser = authStore?.data !== undefined;

  /* NEED AUHTENTICATION */
  if (!authRequired) {
    return next();
  }
  /** AUTHENTICATION */
  if (authpage && !loggeduser) {
    return next({ name: "Login" });
  }
  /* AUTHORIZATION ROLE */
  let authorized = routeTo.meta?.role.includes(authStore?.data?.role);
  if (!authorized) {
    store.dispatch("toast/toastError", {
      title: "Gagal",
      msg: "401 terlarang",
    });
    return next({ path: "/" });
  }
  store.dispatch("spinner/show");
  next();
});

router.beforeResolve(async (routeTo, routeFrom, next) => {
  // Create a `beforeResolve` hook, which fires whenever
  // `beforeRouteEnter` and `beforeRouteUpdate` would. This
  // allows us to ensure data is fetched even when params change,
  // but the resolved route does not. We put it in `meta` to
  // indicate that it's a hook we created, rather than part of
  // Vue Router (yet?).
  try {
    // For each matched route...
    for (const route of routeTo.matched) {
      await new Promise((resolve, reject) => {
        // If a `beforeResolve` hook is defined, call it with
        // the same arguments as the `beforeEnter` hook.
        if (route.meta && route.meta.beforeResolve) {
          route.meta.beforeResolve(routeTo, routeFrom, (...args) => {
            // If the user chose to redirect...
            if (args.length) {
              // If redirecting to the same route we're coming from...
              // Complete the redirect.
              next(...args);
              reject(new Error("Redirected"));
            } else {
              resolve();
            }
          });
        } else {
          // Otherwise, continue resolving the route.
          resolve();
        }
      });
    }
    // If a `beforeResolve` hook chose to redirect, just return.
  } catch (error) {
    return;
  }
  document.title = routeTo.meta.title + " | " + appConfig.title;
  // If we reach this point, continue resolving the route.
  next();
});

router.afterEach(() => {
  store.dispatch("spinner/hide");
});

export default router;
