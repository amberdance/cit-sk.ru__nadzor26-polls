"use strict";

import Vue from "vue";
import axios from "axios";
import router from "../router";
import { responseManage, errorManage } from "../utils/responseManage";

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

axios.interceptors.response.use(
  (response) => responseManage(response),
  (error) => errorManage(error)
);

Plugin.install = (Vue) => {
  Vue.prototype.$isHomePage = () =>
    Boolean(router.history.current.path == "/home");

  Vue.prototype.$HTTPPost = async ({ route, payload, responseType }) => {
    const { data } = await axios.post(route, payload, { responseType });

    if (responseType == "blob") return data;
    if (!data) return [];
    if ("data" in data) return data.data;

    return data;
  };

  Vue.prototype.$HTTPGet = async ({ route, payload }) => {
    const { data } = await axios.get(route, { params: payload });

    return data;
  };
};

Vue.use(Plugin);

export default Plugin;
