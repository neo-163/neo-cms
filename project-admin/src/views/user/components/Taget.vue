<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      :model="postForm"
      :rules="rules"
      class="form-container"
    >
      <sticky :z-index="10" :class-name="'sub-navbar ' + postForm.status">
        <!-- <CommentDropdown v-model="postForm.status" /> -->

        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="submitForm1"
        >
          提交
        </el-button>
      </sticky>

      <div class="createPost-main-container">
        <el-row>
          <el-col :span="24">
            <el-form-item style="margin-bottom: 40px" prop="title">
              <MDinput
                v-model="postForm.title"
                :maxlength="100"
                name="name"
                required
              >
                密码
              </MDinput>
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item
          prop="content"
          style="margin-bottom: 30px"
          v-if="showJson"
        >
          <label class="json-textarea-text">Json 数据</label>
          <textarea
            class="form-control"
            id="json-textarea"
            rows="8"
            v-model="postForm.json"
          ></textarea>
        </el-form-item>

        <el-form-item prop="image_uri" style="margin-bottom: 30px">
          <el-button
            plain
            style="margin-left: 10px"
            @click="dialogTableVisible = true"
          >
            媒体
          </el-button>
        </el-form-item>

        <el-dialog
          width="90%"
          top="15vh"
          title="媒体 |"
          :visible.sync="dialogTableVisible"
        >
          <el-button plain style="position: absolute; top: 15px; left: 100px"  @click="showMediaListAction">
            媒体
          </el-button>

          <el-button plain style="position: absolute; top: 15px; left: 180px"  @click="showMediaUploadAction">
            上传
          </el-button>

          <div id="media-list-box" v-if="showMediaList">
            111
          </div>

          <div id="media-upload-box" v-if="showMediaUpload">
            222
          </div>
        </el-dialog>
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

const defaultForm = {
  status: "draft",
  title: "", // 页面题目
  content: "", // 页面内容
  content_short: "", // 页面摘要
  // url_tag: '', // 页面外链
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
        title: [{ validator: validateRequire }],
        // url_tag: [{ validator: validateRequire }],
        // url_tag: [{ validator: validateSourceUri, trigger: 'blur' }]
      },
      tempRoute: {},
      showJson: false,
      showMediaList: true,
      showMediaUpload: false,
      gridData: [
        {
          date: "2016-05-02",
          name: "王小虎",
          address: "上海市普陀区金沙江路 1518 弄",
        },
        {
          date: "2016-05-04",
          name: "王小虎",
          address: "上海市普陀区金沙江路 1518 弄",
        },
        {
          date: "2016-05-01",
          name: "王小虎",
          address: "上海市普陀区金沙江路 1518 弄",
        },
        {
          date: "2016-05-03",
          name: "王小虎",
          address: "上海市普陀区金沙江路 1518 弄",
        },
      ],
      dialogTableVisible: false,
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
    displayTime: {
      // set and get is useful when the data
      // returned by the back end api is different from the front end
      // back end return => "2013-06-25 06:59:25"
      // front end need timestamp => 1372114765000
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

    // Why need to make a copy of this.$route here?
    // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
    // https://github.com/PanJiaChen/vue-element-admin/issues/1221
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    showMediaListAction() {
      this.showMediaList = true;
      this.showMediaUpload = false;
    },
    showMediaUploadAction() {
      this.showMediaList = false;
      this.showMediaUpload = true;
    },
    fetchData(id) {
      fetchItem(id)
        .then((response) => {
          this.postForm = response.data;

          this.postForm.json = JSON.stringify(this.postForm.json);

          // just for test
          this.postForm.title;
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
    mediaBox() {
      console.log(111);
    },
    submitForm1() {
      if (this.postForm.json == undefined || this.postForm.json == "") {
        this.postForm.json = "[]";
      }

      const json = this.postForm.json;
      if (
        (json.charAt(0) != "{" || json.substr(-1) != "}") &&
        (json.charAt(0) != "[" || json.substr(-1) != "]")
      ) {
        this.notJson();
        return false;
      }
      if (!(typeof JSON.parse(json) === "object")) {
        this.notJson();
        return false;
      }

      if (this.postForm.id == undefined) {
        this.createItemOption();
      } else {
        this.editItemOption();
      }
    },
    notJson() {
      this.$message({
        message: "提交失败，“Json 数据” 请输入json或者array格式的数据",
        type: "warning",
        showClose: true,
        duration: 2500,
      });
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

#json-textarea {
  width: 100%;
}
</style>
