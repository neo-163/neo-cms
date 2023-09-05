<template>
  <el-dropdown :show-timeout="100" trigger="click">
    <el-button plain>
      链接标签url_tag
      <i class="el-icon-caret-bottom el-icon--right" />
    </el-button>
    <el-dropdown-menu slot="dropdown" class="no-padding no-border" style="width:400px">
      <el-form-item label-width="0px" style="margin-bottom: 0px" prop="url_tag">
        <el-input v-model="url_tag" placeholder="">
          <template slot="prepend">
            链接标签
          </template>
        </el-input>
      </el-form-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  computed: {
    url_tag: {
      get() {
        return this.value
      },
      set(val) {
        this.$emit('input', val)
      }
    }
  }
}
</script>
