"use strict";

import Vue from "vue";
import axios from "axios";
import router from "../router";
import { API_BASE_URL } from "../config";

axios.defaults.baseURL = API_BASE_URL;

Plugin.install = Vue => {
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
