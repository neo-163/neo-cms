<template>
  <div class="components-container">
    <el-drag-select v-model="roleValue" style="width:500px;" multiple placeholder="请选择">
      <el-option v-for="item in options" :key="item.roleValue" :label="item.label" :value="item.value" />
    </el-drag-select>

    <div style="margin-top:30px;">
      <el-tag v-for="item of roleValue" :key="item" style="margin-right:15px;">
        {{ item }}
      </el-tag>
    </div>
  </div>
</template>

<script>
import ElDragSelect from '@/components/DragSelect' // base on element-ui

export default {
  name: 'DragSelectDemo',
  components: { ElDragSelect },
  data() {
    return {
      roleValue: ['Apple', 'Banana', 'Orange'],
      options: [{
        value: 'Apple',
        label: 'Apple'
      }, {
        value: 'Banana',
        label: 'Banana'
      }, {
        value: 'Orange',
        label: 'Orange'
      }, {
        value: 'Pear',
        label: 'Pear'
      }, {
        value: 'Strawberry',
        label: 'Strawberry'
      }]
    }
  }
}
</script>
