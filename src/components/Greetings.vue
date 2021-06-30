<template>
  <div :class="$style.wrapper">
    <h1>Уважаемые жители многоквартирных домов!</h1>
    <p>Просим оценить работу вашей управляющей компании по 20 критериям.</p>
    <p>
      Ваше мнение будет учтено при составлении рейтинга управляющих компаний
      Ставропольского края.
    </p>
  </div>
</template>
<script>
export default {
  props: {
    voteCount: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      finalDate: "05.31.2021"
    };
  },

  computed: {
    declinationOfDay() {
      return this.declOfNum(this.daysLeft, ["день", "дня", "дней"]);
    },

    daysLeft() {
      return Math.floor((new Date(this.finalDate) - new Date()) / 86400000);
    }
  },

  methods: {
    declOfNum(number, titles) {
      const cases = [2, 0, 1, 1, 1, 2];

      return titles[
        number % 100 > 4 && number % 100 < 20
          ? 2
          : cases[number % 10 < 5 ? number % 10 : 5]
      ];
    }
  }
};
</script>
<style module>
.wrapper {
  background-color: #ffffffd1;
  text-align: center;
  padding: 0.5rem 0;
}
.wrapper h1 {
  text-transform: uppercase;
}
.digitWrapper {
  display: flex;
  justify-content: space-evenly;
}
.digit {
  font-weight: bold;
  font-size: 40px;
  margin-right: 0.5rem;
}
</style>
