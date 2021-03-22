import Vue from "vue";
import VueRouter from "vue-router";

const originalPush = VueRouter.prototype.push;

VueRouter.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => err);
};

Vue.use(VueRouter);

const routes = [
  {
    path: "*",
    component: () => import("@/components/NotFound")
  },

  { path: "/", redirect: "/home" },

  {
    path: "/home",
    component: () => import("@/views/Home")
  },

  {
    path: "/result/:token",
    component: () => import("@/components/Result")
  },

  {
    path: "/thanks",
    component: () => import("@/components/Thanks")
  }
];

const router = new VueRouter({
  base: process.env.BASE_URL,
  routes
});

export default router;
