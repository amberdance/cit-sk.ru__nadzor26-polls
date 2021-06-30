<template>
  <MainLayout>
    <div :class="$style.wrapper">
      <h1 style="text-align:center;text-transform:uppercase;">
        Благодарим за участие в опросе!
      </h1>
      <div class="text-left">
        <p>
          Опрос по качеству работы управляющих организаций окончен 30 июня 2021 года. Результаты опроса будут опубликованы на сайте управления Ставропольского края - государственной жилищной инспекции.
          <a href="http://nadzor26.ru" target="_blank">www.nadzor26.ru</a>
        </p>

      </div>
    </div>
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
  }
};
</script>

<style module>
.wrapper {
  display: flex;
  flex-direction: column;
  margin-top: 5%;
  background-color: #ffffffd1;
  padding: 0.5rem;
}
</style>
