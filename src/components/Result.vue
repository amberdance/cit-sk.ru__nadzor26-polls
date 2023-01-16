<template>
  <MainLayout>
    <div class="table_wrapper" v-loading.fullscreen.lock="isLoading">
      <div style="margin: 1rem 0; text-align: center">
        <el-switch
          v-model="isValid"
          active-text="с учетом сегмента РФ"
          inactive-text="без учета сегмента РФ"
        ></el-switch>
      </div>

      <el-table :data="tableData" :row-class-name="rowClassName">
        <el-table-column label="организация" prop="label" sortable />
        <el-table-column label="ip" prop="ip" width="200" align="center" />
        <el-table-column
          label="количество голосов"
          prop="count"
          align="center"
          width="200"
          sortable
        />
        <el-table-column
          label="рейтинг"
          prop="score"
          width="200"
          align="center"
          sortable
        />
      </el-table>
    </div>
  </MainLayout>
</template>
<script>
import MainLayout from "@/components/Layouts/MainLayout";

export default {
  components: {
    MainLayout,
  },

  data() {
    return {
      isLoading: false,
      isValid: true,
      scoreList: [],
    };
  },

  computed: {
    tableData() {
      return this.scoreList.filter(({ isValid }) =>
        this.isValid ? isValid == "1" : isValid == "0"
      );
    },
  },

  async created() {
    this.isLoading = true;

    try {
      this.scoreList =
        (await this.$HTTPGet({
          route: "/polls/get-result",
          payload: { token: this.$route.params.token, isValid: this.isValid },
        })) ?? [];
    } catch (e) {
      this.$router.push("/who-i-am");
    } finally {
      this.isLoading = false;
    }
  },

  methods: {
    rowClassName({ row }) {
      if (Number(row.score) < 30) return "danger_row";
      else if (Number(row.score) >= 30 && Number(row.score) < 70)
        return "warning_row";
      else if (Number(row.score) >= 70) return "success_row";
    },
  },
};
</script>
