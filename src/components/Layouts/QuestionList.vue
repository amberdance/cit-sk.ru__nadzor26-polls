<template>
  <div>
    <div
      v-for="item in questions.groups"
      :key="item.id"
      :class="$style.group_wrapper"
    >
      <div>{{ item.id }}: {{ item.label }}:</div>
      <div
        v-for="item in questions.items"
        :key="item.id"
        :class="$style.item_wrapper"
      >
        {{ item.label }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    questions() {
      return this.$store.getters["questions"];
    }
  },

  created() {
    console.log(this.questions.groups);
  }
};
</script>

<style module>
.group_wrapper {
  margin-bottom: 1rem;
}
.item_wrapper {
  padding: 0 1rem;
}
</style>
