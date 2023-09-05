<template>
  <div class="icons-container">
    <el-tabs type="border-card">
      <el-tab-pane label="基本设置">
        <Details :is-edit="false" />
      </el-tab-pane>

      <el-tab-pane label="密码">
        <Taget :is-edit="false" />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import Details from "./components/Details";
import Taget from "./components/Taget";

export default {
  name: "CreateUser",
  components: {
    Details,
    Taget,
  },
};
</script>

<style lang="scss" scoped>
.icons-container {
  margin: 10px 20px 0;
  overflow: hidden;
}
</style>
