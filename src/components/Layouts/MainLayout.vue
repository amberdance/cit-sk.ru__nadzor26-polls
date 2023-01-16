<template>
  <el-container>
    <el-header style="height: auto; z-index: 999; padding: 0">
      <HeaderLayout />
    </el-header>

    <el-container>
      <el-main class="content_wrapper">
        <slot />
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import HeaderLayout from "./HeaderLayout";

export default {
  components: {
    HeaderLayout,
  },
};
</script>
