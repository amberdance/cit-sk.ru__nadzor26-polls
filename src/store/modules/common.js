import { dispatch } from "../../utils/api";
import commonMutations from "@/store/commonMutations";

export default {
  state: {
    companies: [],
    questions: []
  },

  getters: {
    companies: state => Object.values(state.companies),
    questions: state => state.questions
  },

  mutations: {
    setQuestions(state, payload) {
      state.questions = payload;
    },

    ...commonMutations
  },

  actions: {
    async loadCompanies({ commit }, payload) {
      commit("clear", "companies");

      const companies = await dispatch.HTTPGet({
        route: "/polls/get-companies",
        payload
      });

      companies.forEach(property =>
        commit("set", { key: "companies", props: property })
      );
    },

    async loadQuestions({ commit }, payload) {
      commit(
        "setQuestions",
        await dispatch.HTTPGet({
          route: "/polls/get-questions",
          payload
        })
      );
    }
  }
};
