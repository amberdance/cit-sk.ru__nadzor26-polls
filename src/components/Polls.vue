<template>
  <div v-loading.fullscreen.lock="isLoading" :class="$style.wrapper">
    <el-form :model="formData" :rules="formRules" ref="form">
      <div :class="$style.step_wrapper">
        <div :class="$style.step">1</div>
        <div>Выберите управляющую компанию</div>

        <transition name="el-fade-in-linear">
          <div v-if="formData.companyId" style="margin-left:auto;">
            <el-button type="primary" size="small" @click="vote"
              >проголосовать</el-button
            >
          </div>
        </transition>
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
            style="width:100%;"
            no-data-text="введите ключевые слова"
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
          <div v-show="selectedCompany.id" :class="$style.companyDescription">
            <div>
              <span>Наименование:</span><span>{{ selectedCompany.label }}</span>
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
            <QuestionList />
          </div>
        </div>
      </transition>
    </el-form>
  </div>
</template>

<script>
import QuestionList from "@/components/Layouts/QuestionList";

export default {
  components: {
    QuestionList
  },

  data() {
    return {
      isLoading: false,
      filterCompanies: [],

      formData: {
        companyId: null
      },

      formRules: {
        companyId: [
          { requred: true, message: "не выбрана управляющая компания" }
        ]
      }
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
    }
  },

  async created() {
    this.isLoading = true;
    try {
      await this.$store.dispatch("loadCompanies");
      await this.$store.dispatch("loadQuestions");
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
    },

    purge() {
      this.$refs.form.resetFields();
    },

    companyFilter(val) {
      return (this.filterCompanies = this.companies.filter(
        item =>
          item.label.toLowerCase().includes(val.toLowerCase()) ||
          item.inn.includes(val) ||
          item.ogrn.includes(val)
      ));
    }
  }
};
</script>

<style module>
.wrapper {
  background-color: #ffffffd4;
  min-height: 70%;
  padding: 1rem;
  border: 1px #efefef solid;
  box-shadow: 1px 1px 5px 3px #8080800f;
}
.step_wrapper {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #dadada;
  margin-bottom: 1rem;
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
}
.inner select,
.inner input {
  width: 100% !important;
}
.companyDescription {
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
</style>
