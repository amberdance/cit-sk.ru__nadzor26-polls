<template>
  <MainLayout>
    <div :class="$style.wrapper">{{ title }}</div>
  </MainLayout>
</template>

<script>
import MainLayout from "@/components/Layouts/MainLayout";

export default {
  components: {
    MainLayout
  },

  props: {
    isVoted: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  computed: {
    title() {
      return this.isVoted
        ? "Благодарим за участие в опросе! Ваш голос был учтен"
        : "Благодарим за участие в опросе!";
    }
  }
};
</script>

<style module>
.wrapper {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
