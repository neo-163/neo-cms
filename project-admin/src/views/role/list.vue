<template>
  <div class="app-container">
    <el-button type="primary" @click="handleAddRole">新增角色</el-button>

    <el-table :data="rolesList" style="width: 100%;margin-top:30px;" border>
      <el-table-column align="center" label="名称" width="220">
        <template slot-scope="scope">
          {{ scope.row.title }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="用户数" width="220">
        <template slot-scope="scope">
          {{ scope.row.status }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="状态" width="220">
        <template slot-scope="scope">
          {{ scope.row.status }}
        </template>
      </el-table-column>
      <el-table-column align="header-center" label="描述">
        <template slot-scope="scope">
          {{ scope.row.description }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作">
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click="handleEdit(scope)">编辑</el-button>
          <el-button type="danger" size="small" @click="handleDelete(scope)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'编辑角色':'新增角色'">
      <el-form :model="role" label-width="80px" label-position="left">
        <el-form-item label="标题">
          <el-input v-model="role.title" placeholder="" />
        </el-form-item>
        <el-form-item label="描述">
          <el-input v-model="role.description" placeholder="" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input v-model="role.sort" placeholder="" />
        </el-form-item>
        <el-form-item label="权限">
          <el-tree
            :data="routesData"
            show-checkbox
            node-key="id"
            :default-expanded-keys="[2, 3]"
            :default-checked-keys=rolePermissions
            :props="defaultProps"
            @check="currentChecked"
          />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="dialogVisible=false">取消</el-button>
        <el-button type="primary" @click="confirmRole">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import path from 'path'
import { getRoutes, fetchList, addRole, deleteRole, updateRole } from '@/api/role'

const defaultRole = {
  key: '',
  title: '',
  description: '',
  routes: []
}

export default {
  data() {
    return {
      role: Object.assign({}, defaultRole),
      routes: [],
      rolesList: [],
      dialogVisible: false,
      dialogType: 'new',
      checkStrictly: false,
      defaultProps: {
        child: 'children',
        title: 'label'
      },
      rolePermissions: [5,6],
    }
  },
  computed: {
    routesData() {
      return this.routes
    }
  },
  created() {
    // Mock: get all routes and roles list from server
    this.getRoutes()
    this.fetchList()
  },
  methods: {
    currentChecked (nodeObj, SelectedObj) {
      this.rolePermissions = SelectedObj.checkedKeys // 这是选中的节点的key数组
    },
    async getRoutes() {
      const res = await getRoutes()
      this.serviceRoutes = res.data
      this.routes = res.data
    },
    async fetchList() {
      const res = await fetchList()
      this.rolesList = res.data.data
    },
    generateArr(routes) {
      let data = []
      routes.forEach(route => {
        data.push(route)
        if (route.child) {
          const temp = this.generateArr(route.child)
          if (temp.length > 0) {
            data = [...data, ...temp]
          }
        }
      })
      return data
    },
    handleAddRole() {
      this.role = Object.assign({}, defaultRole)
      if (this.$refs.tree) {
        this.$refs.tree.setCheckedNodes([])
      }
      this.dialogType = 'new'
      this.dialogVisible = true
    },
    handleEdit(scope) {
      this.dialogType = 'edit'
      this.dialogVisible = true
      this.checkStrictly = true
      this.role.title = scope.row.title
      this.role.description = scope.row.description
      this.rolePermissions= [5,6,7,8,9,10]

    },
    handleDelete({ $index, row }) {
      this.$confirm('是否确定删除当前用户？', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(async() => {
          await deleteRole(row.key)
          this.rolesList.splice($index, 1)
          this.$message({
            type: 'success',
            message: 'Delete succed!'
          })
        })
        .catch(err => { console.error(err) })
    },
    async confirmRole() {
      const isEdit = this.dialogType === 'edit'

      if (isEdit) {
        await updateRole(this.role.key, this.role)
        console.log(this.role.key)
        console.log(this.role)
      } else {
        const { data } = await addRole(this.role)
        this.role.key = data.key
        this.rolesList.push(this.role)
      }

      const { description, key, name } = this.role
      this.dialogVisible = false
      this.$notify({
        title: 'Success',
        dangerouslyUseHTMLString: true,
        message: `
            <div>Role Key: ${key}</div>
            <div>Role Name: ${name}</div>
            <div>Description: ${description}</div>
          `,
        type: 'success'
      })
    },
    // reference: src/view/layout/components/Sidebar/SidebarItem.vue
    onlyOneShowingChild(child = [], parent) {
      let onlyOneChild = null
      const showingchild = child.filter(item => !item.hidden)

      // When there is only one child route, the child route is displayed by default
      if (showingchild.length === 1) {
        onlyOneChild = showingchild[0]
        onlyOneChild.path = path.resolve(parent.path, onlyOneChild.path)
        return onlyOneChild
      }

      // Show parent if there are no child route to display
      if (showingchild.length === 0) {
        onlyOneChild = { ... parent, path: '', noShowingchild: true }
        return onlyOneChild
      }

      return false
    }
  }
}
</script>

<style lang="scss" scoped>
.app-container {
  .roles-table {
    margin-top: 30px;
  }
  .permission-tree {
    margin-bottom: 30px;
  }
}
</style>
