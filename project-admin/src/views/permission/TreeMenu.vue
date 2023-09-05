<template>
  <ul class="tree-menu-ul">
    <li v-for="(row, index) in ruleList" :key="index">
      <div class="tree-menu-li">
        <span class="tree-menu-left-text">
          <el-tag class="ml-2" effect="light">ID: {{ row.id }}</el-tag>
        </span>
        <span class="tree-menu-left-text">
          {{ row.title }}
        </span>
        <span class="tree-menu-left-text">
          <el-tag class="ml-2" type="success" v-if="row.status == 1" effect="dark">启用</el-tag>
        <el-tag class="ml-2" type="warning" v-if="row.status == 2" effect="dark">停用</el-tag>
        </span>
        <span class="tree-menu-left-text">
          {{ row.route }}
        </span>
        
        <span class="tree-menu-btn">
          <el-button type="primary" size="mini" @click="upateChildItem(row)">
            编辑
          </el-button>
          <el-button v-if="row.status!='deleted'" size="mini" type="danger" @click="deleteChildItem(row)">
            删除
          </el-button>
        </span>
      </div>

      <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible1">

      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="70px" style="width: 400px; margin-left:50px;">
        
        <el-form-item label="标题" prop="title">
          <el-input v-model="temp.title" />
        </el-form-item>

        <el-form-item label="路由" prop="route">
          <el-input v-model="temp.route" />
        </el-form-item>

        <el-form-item label="排序" prop="sort">
          <el-input v-model="temp.sort" />
        </el-form-item>
        
        <el-form-item label="父级">
          <el-select v-model="temp.parent" class="m-2" placeholder="Select" size="large" style="width:100%;">
            <el-option v-for="item in parentOptions" :key="item.parent" :label="item.label" :value="item.id" />
          </el-select>

        </el-form-item>
        
        <el-form-item label="状态">
          <el-select v-model="temp.status" class="m-2" placeholder="Select" size="large" style="width:100%;">
            <el-option v-for="item in statusOptions" :key="item.status" :label="item.label" :value="item.value" />
          </el-select>
        </el-form-item>
      
        <el-form-item label="描述" prop="description">
          <el-input v-model="temp.description" :autosize="{ minRows: 2, maxRows: 4}" type="textarea" placeholder="描述" />
        </el-form-item>

      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible1 = false">
          取消
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateChildData()">
          提交
        </el-button>
      </div>

    </el-dialog>

      <treeMenu :ruleList="row.children"></treeMenu>
    </li>
  </ul>
</template>
 
<script lang="ts">
import { fetchList, storeItem, deleteItem, getParentSelectList } from '@/api/permission'

export default {
  name: "treeMenu",
  props: ["ruleList"],
  data() {
    return {
      temp: {
        id: undefined,
        title: '',
        route: '',
        parent: undefined,
        sort: 0,
        status: 1,
        description: '',
      },
      dialogFormVisible1: false,
      dialogStatus: '',
      textMap: {
        update: '编辑',
        create: '创建'
      },
      rules: {
        title: [{ required: true, message: 'title必填', trigger: 'blur' }],
        route: [{ required: true, message: 'route必填', trigger: 'change' }],
        sort: [{ required: true, message: 'route必填', trigger: 'blur' }],
      },
      downloadLoading: false,
      parentOptions: [],
      status: 1,
      statusOptions: [
        { value: 1, label: '启用' }, 
        { value: 2, label: '停用' }
      ],
      levelClass: '1',
    }
  },
  methods: {
    upateChildItem(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.getParentSelectList(row)
      this.temp.timestamp = new Date(this.temp.timestamp)
      this.dialogStatus = 'update'
      this.dialogFormVisible1 = true
    },
    getParentSelectList(row) {
      getParentSelectList(row.id).then(response => {
        this.parentOptions = response.data
      })
    },
    updateChildData() {
      const tempData = Object.assign({}, this.temp)
      tempData.timestamp = +new Date(tempData.timestamp) // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
      storeItem(tempData).then(() => {
        // const index = this.ruleList.findIndex(v => v.id === this.temp.id)
        // this.ruleList.splice(index, 1, this.temp)
        // this.getList()
        this.$router.go(0)
        this.dialogFormVisible1 = false
        this.$notify({
          title: '成功',
          message: '更新成功',
          type: 'success',
          duration: 2000
        })
      })
    },
    deleteChildItem(row) {
      const x = confirm('是否删除ID为 '+row.id+'，标题为 “'+row.title+'” 的内容');
      if(x) {
        let arr = [];
        arr.push(row.id);
        console.log(arr);
        deleteItem(arr).then(response => {
          if(response.code == 200) {
            this.$notify({
              title: '成功',
              message: '删除成功',
              type: 'success',
              duration: 2000
            })
            // this.ruleList.splice(index, 1)
            this.getList()
          } else {
            this.$notify({
              title: '警告',
              message: '删除失败',
              type: 'warning',
              duration: 2000
            })
          }
        })
      }
    },
    getList() {
      fetchList(this.listQuery).then(response => {
        // 修复动态修改props值的bug
        this.ruleList = response.data
        console.log(response.data)
      })
    },
  },
};
</script>
 
<style scoped>
ul {
  list-style: none;
  padding-left: 30px;
}
.tree-menu-li {
    background: #f0f0f1;
    padding: 10px;
    margin: 7px 0;
}
.tree-menu-btn {
    float: right;
}
.tree-menu-left-text {
    margin-right: 10px;
}
</style>