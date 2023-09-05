<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      :model="postForm"
      :rules="rules"
      class="form-container"
    >
      <sticky :z-index="10" :class-name="'sub-navbar ' + postForm.status">
        <CommentDropdown v-model="postForm.status" />

        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="submitForm"
        >
          提交
        </el-button>
      </sticky>

      <div class="createPost-main-container">
        <el-row>
          <el-col :span="24">
            <el-form-item style="margin-bottom: 40px" prop="username">
              <MDinput
                v-model="postForm.username"
                :maxlength="100"
                name="name"
                required
              >
                标题
              </MDinput>
            </el-form-item>
          </el-col>
        </el-row>
        
        <el-form-item label="角色">
          <el-drag-select v-model="adminRoleValue" style="width:100%;" multiple placeholder="请选择">
            <el-option v-for="item in options" :key="item.adminRoleValue" :label="item.label" :value="item.value" />
          </el-drag-select>
        </el-form-item>

        <div style="margin-top:30px;" hidden>
          <el-tag v-for="item of adminRoleValue" :key="item" style="margin-right:15px;">
            {{ item }}
          </el-tag>
        </div>

        <el-form-item label="简介">
          <el-input v-model="postForm.description" :autosize="{ minRows: 2, maxRows: 4}" type="textarea" placeholder="" />
        </el-form-item>

        <!-- <el-form-item prop="image_uri" style="margin-bottom: 30px;">
          <Upload v-model="postForm.image_uri" />
        </el-form-item> -->
      </div>
    </el-form>
  </div>
</template>

<script>
import Tinymce from "@/components/Tinymce";
import Upload from "@/components/Upload/SingleImage3";
import MDinput from "@/components/MDinput";
import Sticky from "@/components/Sticky"; // 粘性header组件
// import { validURL } from '@/utils/validate'
import { fetchItem, createItem, editItem } from "@/api/user";
import { searchUser } from "@/api/remote-search";
import {
  CommentDropdown,
  PlatformDropdown,
  SourceUrlDropdown,
} from "./Dropdown";

import ElDragSelect from '@/components/DragSelect' // base on element-ui

const defaultForm = {
  username: "",
  description: "", // 页面内容
  content_short: "", // 页面摘要
  image_uri: "", // 页面图片
  display_time: undefined, // 前台展示时间
  id: undefined,
  platforms: ["a-platform"],
  status: 1,
};

export default {
  name: "ArticleDetail",
  components: {
    Tinymce,
    MDinput,
    Upload,
    Sticky,
    CommentDropdown,
    PlatformDropdown,
    SourceUrlDropdown,
    ElDragSelect,
  },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const validateRequire = (rule, value, callback) => {
      if (value === "") {
        this.$message({
          message: rule.field + "为必传项",
          type: "error",
        });
        callback(new Error(rule.field + "为必传项"));
      } else {
        callback();
      }
    };
    const validateSourceUri = (rule, value, callback) => {
      if (value) {
        // if (validURL(value)) {
        if (1 == 1) {
          callback();
        } else {
          this.$message({
            message: "外链url填写不正确1",
            type: "error",
          });
          callback(new Error("外链url填写不正确2"));
        }
      } else {
        callback();
      }
    };
    return {
      postForm: Object.assign({}, defaultForm),
      loading: false,
      userListOptions: [],
      rules: {
        // image_uri: [{ validator: validateRequire }],
        username: [{ validator: validateRequire }],
      },
      tempRoute: {},
      adminRoleValue: [1, 2, 3],
      options: [{
        value: 1,
        label: 'Apple'
      }, {
        value: 2,
        label: 'Banana'
      }, {
        value: 3,
        label: 'Orange'
      }, {
        value: 4,
        label: 'Pear'
      }, {
        value: 5,
        label: 'Strawberry'
      }],
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
    displayTime: {
      get() {
        return +new Date(this.postForm.display_time);
      },
      set(val) {
        this.postForm.display_time = new Date(val);
      },
    },
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    }

    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    fetchData(id) {
      fetchItem(id)
        .then((response) => {
          this.postForm = response.data;
          console.log(this.postForm)

          // just for test
          this.postForm.username;
          // += `   Article Id:${this.postForm.id}`
          this.postForm.content_short += `   Article Id:${this.postForm.id}`;

          // set tagsview title
          this.setTagsViewTitle();

          // set page title
          this.setPageTitle();
        })
        .catch((err) => {
          console.log(err);
        });
    },
    setTagsViewTitle() {
      const title = "编辑课程";
      const route = Object.assign({}, this.tempRoute, {
        title: `${title}-${this.postForm.id}`,
      });
      this.$store.dispatch("tagsView/updateVisitedView", route);
    },
    setPageTitle() {
      const title = "编辑课程";
      document.title = `${title} - ${this.postForm.id}`;
    },

    createItemOption() {
      createItem(this.postForm)
        .then((response) => {
          // console.log(response)
          this.returnArticleTigs(response);
          if (response.code == 200) {
            this.$router.push("/user/edit/" + response.data.id);
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    editItemOption() {
      editItem(this.postForm)
        .then((response) => {
          // console.log(response)
          this.returnArticleTigs(response);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    returnArticleTigs(response) {
      if (response.code == 200) {
        response.tipsType = "success";
        response.tipsTitle = "成功";
      } else {
        response.tipsType = "warning";
        response.tipsTitle = "失败";
      }
      this.$refs.postForm.validate((valid) => {
        if (valid) {
          this.loading = true;
          this.$notify({
            title: response.tipsTitle,
            message: response.message,
            type: response.tipsType,
            duration: 2000,
          });
          this.loading = false;
        } else {
          // console.log(response.message)
          return false;
        }
      });
    },
    submitForm() {
      if (this.postForm.id == undefined) {
        this.createItemOption();
      } else {
        this.editItemOption();
      }
    },
    getRemoteUserList(query) {
      searchUser(query).then((response) => {
        if (!response.data.items) return;
        this.userListOptions = response.data.items.map((v) => v.name);
      });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "~@/styles/mixin.scss";

.createPost-container {
  position: relative;

  .createPost-main-container {
    padding: 40px 45px 20px 50px;

    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;

      .postInfo-container-item {
        float: left;
      }
    }
  }

  .word-counter {
    width: 40px;
    position: absolute;
    right: 10px;
    top: 0px;
  }
}

.article-textarea ::v-deep {
  textarea {
    padding-right: 40px;
    resize: none;
    border: none;
    border-radius: 0px;
    border-bottom: 1px solid #bfcbd9;
  }
}
</style>
