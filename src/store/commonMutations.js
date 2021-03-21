import Vue from "vue";

export default {
  set(state, { key, props }) {
    Vue.set(state[key], props.id, props);
  },

  clear(state, key) {
    state[key] = {};
  },

  update(state, { key, props }) {
    for (let prop in props) {
      Vue.set(state[key][props.id], prop, props[prop]);
    }
  },

  remove(state, { key, id }) {
    Vue.delete(state[key], id);
  }
};
