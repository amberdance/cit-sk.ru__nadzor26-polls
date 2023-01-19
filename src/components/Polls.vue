<template>
  <Main-layout>
    <div class="container">
      <div v-loading.fullscreen.lock="isLoading" :class="$style.wrapper">
        <Greetings :vote-count="voteCount" />

        <el-form :model="formData" :rules="formRules" ref="form">
          <div :class="$style.step_wrapper">
            <div :class="$style.step">1</div>
            <div>Выберите управляющую компанию</div>
          </div>

          <div :class="$style.inner">
            <el-form-item
              label="Наименование управляющей компании:"
              prop="companyId"
            >
              <el-select
                v-model="formData.companyId"
                placeholder="ИНН, ОГРН, наименование"
                filterable
                clearable
                style="width: 100%"
                no-data-text="введите ключевые слова"
                no-match-text="ничего не найдено"
                :filter-method="companyFilter"
                @clear="filterCompanies = []"
              >
                <el-option
                  v-for="item in filterCompanies"
                  :label="item.label"
                  :value="item.id"
                  :key="item.id"
                ></el-option>
              </el-select>
            </el-form-item>

            <transition name="el-fade-in-linear">
              <div
                v-show="selectedCompany.id"
                :class="$style.companyDescription"
              >
                <div>
                  <span>Наименование:</span
                  ><span>{{ selectedCompany.label }}</span>
                </div>
                <div>
                  <span>Юридический адрес:</span
                  ><span>{{ selectedCompany.address }}</span>
                </div>
                <div>
                  <span>ИНН:</span><span>{{ selectedCompany.inn }}</span>
                </div>
                <div>
                  <span>ОГРН:</span><span>{{ selectedCompany.ogrn }}</span>
                </div>
              </div>
            </transition>
          </div>

          <transition name="el-fade-in-linear">
            <div v-show="formData.companyId">
              <div :class="$style.step_wrapper">
                <div :class="$style.step">2</div>
                <div>Ответьте на вопросы</div>
              </div>

              <div :class="$style.inner">
                <QuestionList :scoreResult="scoreResult" />
              </div>
            </div>
          </transition>

          <transition name="el-fade-in-linear">
            <div v-if="formData.companyId" :class="$style.vote">
              <el-button type="success" @click="vote">Проголосовать</el-button>
            </div>
          </transition>
        </el-form>
      </div>
    </div>
  </Main-layout>
</template>

<script>
import MainLayout from "@/components/Layouts/MainLayout";
import QuestionList from "@/components/QuestionList";
import Greetings from "@/components/Greetings";

export default {
  components: {
    MainLayout,
    QuestionList,
    Greetings,
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
      isLoading: false,
      voteCount: 0,
      scoreResult: {},
      filterCompanies: [],

      formData: {
        companyId: null,
      },

      formRules: {
        companyId: [
          { requred: true, message: "не выбрана управляющая компания" },
        ],
      },
    };
  },

  computed: {
    companies() {
      return Object.freeze(this.$store.getters["companies"]);
    },

    selectedCompany() {
      return (
        this.companies.filter(({ id }) => id == this.formData.companyId)[0] ??
        []
      );
    },
  },

  async created() {
    if (this.isVoted) return this.$router.push("Thanks");

    this.isLoading = true;

    try {
      await this.$store.dispatch("loadCompanies");
      await this.$store.dispatch("loadQuestions");

      const { data } = await this.$HTTPGet({
        route: "/polls/get-vote-count",
      });

      this.voteCount = data.count;
    } catch (e) {
      console.error(e);
      this.$message.error({ message: e });
    } finally {
      this.isLoading = false;
    }
  },

  methods: {
    async vote() {
      await this.$refs.form.validate();

      if (!this.isScoresPositive())
        await this.$confirm(
          "Не выставлено ни одного положительного балла, все верно?",
          {
            confirmButtonText: "да",
            cancelButtonText: "нет",
          }
        );
      else
        await this.$confirm("Все оценки выставлены корректно?", {
          confirmButtonText: "да",
          cancelButtonText: "нет",
        });

      this.isLoading = true;

      try {
        await this.$HTTPPost({
          route: "/polls/vote",
          payload: {
            companyId: Number(this.formData.companyId),
            scores: this.formatScoreResults(),
          },
        });

        this.purge();
        this.$router.push("thanks");
        this.$message.success("Спасибо, голос учтен");
      } catch (e) {
        if (e.code == 102)
          this.$message.error(`С ip-адреса ${e.error} уже голосовали`);
      } finally {
        this.isLoading = false;
      }
    },

    isScoresPositive() {
      let isScoresPositive = false;

      for (const key in this.scoreResult) {
        if (this.scoreResult[key] > 0) isScoresPositive = true;
      }

      return isScoresPositive;
    },

    formatScoreResults() {
      let result = [];

      for (const key in this.scoreResult) {
        result.push({
          questionId: Number(key),
          score: this.scoreResult[key]
            ? this.scoreResult[key] - 1
            : this.scoreResult[key],
        });
      }

      return result;
    },

    purge() {
      this.$refs.form.resetFields();
    },

    companyFilter(val) {
      return (this.filterCompanies = this.companies.filter(
        (item) =>
          item.label.toLowerCase().includes(val.toLowerCase()) ||
          item.inn.includes(val) ||
          item.ogrn.includes(val)
      ));
    },
  },
};
</script>

<style module>
.wrapper {
  background-color: #ffffff;
  min-height: 100%;
  padding: 1rem;
  border: 1px #efefef solid;
  box-shadow: 1px 1px 5px 3px #8080800f;
}

.step_wrapper {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #dadada;
  margin: 2rem 0;
  padding: 1rem 0;
}

.step {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #23497b;
  height: 30px;
  width: 30px;
  color: white;
  font-size: 18px;
  margin-right: 0.5rem;
}

.inner {
  display: flex;
  align-items: flex-start;
}

.inner select,
.inner input {
  width: 100% !important;
}

.companyDescription {
  padding-top: 11px;
  margin-left: 1rem;
  font-size: 14px;
}

.companyDescription div {
  margin-bottom: 0.5rem;
}

.companyDescription span:first-child {
  margin-right: 0.5rem;
  color: gray;
}

.el-form-item__label {
  padding: 0 !important;
}

.vote {
  text-align: center;
  margin: 1rem 0;
}
</style>
