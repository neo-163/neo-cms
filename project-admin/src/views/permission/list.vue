<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        新增
      </el-button>
      <!-- <el-button v-waves class="filter-item" type="primary" :icon="convertExpandIcon" @click="ConvertOrExpand">
        {{ convertExpandText }}
      </el-button> -->
    </div>
    <el-row>
      <el-col :span="24">
        <div style="margin-left:-30px;">
          <TreeMenu :ruleList="ruleList"></TreeMenu>
        </div>
      </el-col>
    </el-row>
    
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
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

        <el-form-item label="父级" prop="parent">
          <el-select v-model="temp.parent" class="m-2" placeholder="Select" size="large" style="width:100%;">
            <el-option v-for="item in parentOptions" :key="item.parent" :label="item.label" :value="item.value" />
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
        <el-button @click="dialogFormVisible = false">
          取消
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">
          提交
        </el-button>
      </div>
    </el-dialog>
    
  </div>
</template>

<script>
import { fetchList, storeItem, deleteItem } from '@/api/permission'
import waves from '@/directive/waves' // waves directive
import { parseTime } from '@/utils'
import TreeMenu from "./TreeMenu";

export default {
  name: 'ComplexTable',
  components: { TreeMenu },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      listQuery: {
        title: undefined,
        route: '',
        sort: '+id'
      },
      importanceOptions: [1, 2, 3],
      temp: {
        id: undefined,
        title: '',
        route: '',
        parent: 0,
        sort: 0,
        status: 1,
        description: '',
      },
      dialogFormVisible: false,
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
      status: 1,
      parentOptions: [
        { value: 1, label: '启用' }, 
        { value: 2, label: '停用' }
      ],
      statusOptions: [
        { value: 1, label: '启用' }, 
        { value: 2, label: '停用' }
      ],
      ruleList: [],
      convertExpand: true,
      convertExpandIcon: 'el-icon-minus',
      convertExpandText: '折合',
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      fetchList(this.listQuery).then(response => {
        this.ruleList = response.data
        console.log(response.data)
      })
    },
    ConvertOrExpand() {
      if(this.convertExpand) {
        this.convertExpand = false
        this.convertExpandIcon = 'el-icon-plus'
        this.convertExpandText = '展开'
      } else {
        this.convertExpand = true
        this.convertExpandIcon = 'el-icon-minus'
        this.convertExpandText = '折合'
      }
    },
    stopUsed(row, status) {
      this.$message({
        message: '操作Success',
        type: 'success'
      })
      row.status = status
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        title: '',
        route: '',
        parent: 0,
        sort: 0,
        status: 1,
        description: '',
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          storeItem(this.temp).then((response) => {
            console.log(response.data)
            this.getList()
            this.dialogFormVisible = false
            this.$notify({
              title: '成功',
              message: '新增成功',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.temp.timestamp = new Date(this.temp.timestamp)
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp)
          tempData.timestamp = +new Date(tempData.timestamp) // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          storeItem(tempData).then(() => {
            const index = this.list.findIndex(v => v.id === this.temp.id)
            this.list.splice(index, 1, this.temp)
            this.dialogFormVisible = false
            this.$notify({
              title: '成功',
              message: '更新成功',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    },
    handleDelete(row, index) {
      const x = confirm('是否删除ID为 '+row.id+'，标题为 “'+row.title+'” 的内容');
      if(x) {
        console.log(index)
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
            this.list.splice(index, 1)
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
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['timestamp', 'title', 'type', 'importance', 'status']
        const filterVal = ['timestamp', 'title', 'type', 'importance', 'status']
        const data = this.formatJson(filterVal)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list'
        })
        this.downloadLoading = false
      })
    },
    formatJson(filterVal) {
      return this.list.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j])
        } else {
          return v[j]
        }
      }))
    },
    getSortClass: function(key) {
      const sort = this.listQuery.sort
      return sort === `+${key}` ? 'ascending' : 'descending'
    }
  }
}
</script>
