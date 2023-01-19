<template>
  <MainLayout>
    <div class="container">
      <div :class="$style.wrapper">
        <h1 style="text-align: center">Благодарим за участие в опросе!</h1>
        <div class="text-left">
          <p>
            Опрос по качеству работы управляющих организаций окончен
            {{ END_DATE }}. Результаты опроса будут опубликованы на сайте
            управления Ставропольского края - государственной жилищной
            инспекции.
            <a :href="APP_URL" target="_blank">{{ APP_URL }}</a>
          </p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from "@/components/Layouts/MainLayout";

export default {
  components: {
    MainLayout,
  },

  props: {
    isVoted: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  data() {
    return {
      APP_URL: process.env.VUE_APP_URL,
      END_DATE: "30 июня 2023 года",
    };
  },
};
</script>

<style module>
.wrapper {
  display: flex;
  flex-direction: column;
  background-color: #ffffffd1;
  padding: 3rem;
  max-width: 1000px;
  margin: 0 auto;
}

p {
  text-align: center;
  padding-top: 1rem;
}
</style>
