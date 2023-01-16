<template>
  <div>
    <div
      v-for="item in questions"
      :key="item.group.id"
      :class="$style.group_wrapper"
    >
      <div :class="$style.heading">
        {{ item.group.id }}: {{ item.group.label }}:
      </div>

      <div
        v-for="question in item.questions"
        :key="question.id"
        :class="$style.question"
      >
        <div>{{ question.label }}</div>
        <div>
          <el-rate
            v-model="scoreResult[question.id]"
            show-text
            :max="6"
            :colors="colors"
            :class="$style.item_wrapper"
            :texts="['0', '1', '2', '3', '4', '5']"
            score-template="{value:asdasd}"
          >
          </el-rate>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    scoreResult: {
      type: Object,
      requred: true,
    },
  },

  data() {
    return {
      colors: ["#99A9BF", "#F7BA2A", "#FF9900"],
    };
  },

  computed: {
    questions() {
      return this.$store.getters["questions"];
    },
  },
};
</script>

<style module>
.group_wrapper {
  margin-bottom: 1rem;
}
.item_wrapper {
  padding: 0 1rem;
}
.heading {
  text-transform: uppercase;
  padding: 1rem 0;
  margin: 1rem 0;
  font-size: 15px;
  font-weight: bold;
  border-bottom: 1px solid #dadada;
}
.question {
  margin: 1rem 0;
}
.question div:first-child {
  margin-bottom: 1rem;
}
</style>
