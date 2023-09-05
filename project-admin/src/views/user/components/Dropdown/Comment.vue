<template>
  <el-dropdown :show-timeout="100" trigger="click">
    <el-button plain>
      <span v-if="status == 1">状态: 正常</span>
      <span v-if="status == 2">状态: 停用</span>

      <i class="el-icon-caret-bottom el-icon--right" />
    </el-button>
    <el-dropdown-menu slot="dropdown" class="no-padding">
      <el-dropdown-item>
        <el-radio-group v-model="status" style="padding: 10px">
          <el-radio :label="1"> 正常 </el-radio>
          <el-radio :label="2"> 停用 </el-radio>
        </el-radio-group>
      </el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
export default {
  props: {
    value: {
      // type: Boolean,
      default: 0,
    },
  },
  computed: {
    status: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
};
</script>
