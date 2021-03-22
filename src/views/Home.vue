<template>
  <component :is="asyncComponent" :isVoted="isVoted" />
</template>

<script>
import MainLayout from "@/components/Layouts/MainLayout";
import Polls from "@/components/Polls";

export default {
  components: {
    MainLayout,
    Polls
  },

  data() {
    return {
      asyncComponent: null,
      isVoted: false
    };
  },

  async created() {
    const { isVoted } = await this.$HTTPGet({
      route: "/polls/validate"
    });

    this.isVoted = isVoted;
    this.isVoted
      ? (this.asyncComponent = () => import("@/components/Thanks"))
      : (this.asyncComponent = () => import("@/components/Polls"));
  }
};
</script>
