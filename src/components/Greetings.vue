<template>
  <div :class="$style.wrapper">
    <div class="container">
      <h1>Уважаемые жители многоквартирных домов!</h1>
      <p>
        Просим оценить работу вашей управляющей компании по 20 критериям. Ваше
        мнение будет учтено при составлении рейтинга управляющих компаний
        Ставропольского края.
      </p>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    voteCount: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      FINAL_DATE: "05.31.2021",
    };
  },

  computed: {
    declinationOfDay() {
      return this.declOfNum(this.daysLeft, ["день", "дня", "дней"]);
    },

    daysLeft() {
      return Math.floor((new Date(this.FINAL_DATE) - new Date()) / 86400000);
    },
  },

  methods: {
    declOfNum(number, titles) {
      const cases = [2, 0, 1, 1, 1, 2];

      return titles[
        number % 100 > 4 && number % 100 < 20
          ? 2
          : cases[number % 10 < 5 ? number % 10 : 5]
      ];
    },
  },
};
</script>
<style module>
.wrapper {
  background-color: #ffffffd1;
  text-align: center;
  padding: 0.5rem 0;
  border: 2px solid #cf0000;
}
.wrapper p {
  margin: 0 auto;
  padding-top: 1rem;
  max-width: 500px;
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
