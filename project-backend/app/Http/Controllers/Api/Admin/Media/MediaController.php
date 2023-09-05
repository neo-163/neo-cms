<?php

namespace App\Http\Controllers\Api\Admin\Media;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\Media\MediaService;
use Illuminate\Http\Request;
use \App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends BaseController
{
    /**
     * 媒体分类列表
     * @author Neo
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request)
    {
        // 验证参数
        $input = $request->only('page', 'limit', 'title', 'sort', 'status');
        $validationCondition = [
            'page' => 'nullable|integer', // 分页号码
            'limit' => 'nullable|integer', // 每页多少记录
            'title' => 'nullable|string', // 搜索标题
            'sort' => 'nullable|string', // 创建时间排序
            'status' => 'nullable|integer|in:1,2,3', // 搜索状态
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::list($input);
    }

    /**
     * 媒体分类详情
     * @author Neo
     * @param Request $request
     * @return mixed
     */
    public function details(Request $request)
    {
        // 验证参数
        $input = $request->only('id');
        $validationCondition = [
            'id' => 'required|integer',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }
        return MediaService::details($input);
    }

    /**
     * 创建媒体分类
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function create(Request $request)
    {
        // 验证参数
        $input = $request->only('title', 'image', 'content', 'url_tag', 'json', 'status', 'remarks');
        $validationCondition = [
            'title' => 'required|string',
            'image' => 'nullable|integer',
            'content' => 'nullable|string',
            'url_tag' => 'required|string',
            'json' => 'nullable|string',
            'status' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::create($input);
    }

    /**
     * 编辑媒体分类
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function edit(Request $request)
    {
        // 验证参数
        $input = $request->only('id', 'title');
        $validationCondition = [
            'id' => 'required|integer',
            'title' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::edit($input);
    }

    /**
     * 删除
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function delete(Request $request)
    {
        // 验证参数
        $input = $request->only('id');
        $validationCondition = [
            'id' => 'required|array',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::delete($request);
    }

    /**
     * @description: 上传到OSS接口，wmv格式不能上传
     * 后续加密，第二次加载会失效，避免盗链下载
     * @author Neo
     * @param {Request} $request
     * @return {*}
     */
    public function uploadOSSFile(Request $request)
    {
        $validationCondition = [
            'select_file'  => 'required|mimes:jpg,jpeg,png,webp,gif,mp3,mp4,mkv,wmv,xlsx,xls,doc,docx,txt,sql,zip,rar,7z,tar,gz,iso,pdf|max:1024000', // 最大1000MB，约1G
            'platform' => 'required|integer', // 类型：1-后台CMS，2-微信小程序，3-H5，4-安卓，5-IOS，6-其他
        ];
        $validator = Validator::make($request->all(), $validationCondition, []);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::uploadOSSFile($request);
    }

    /**
     * @description: 可批量删除OSS媒体
     * @author Neo
     * @param {Request} $request
     * @return {*}
     */
    public static function deleteOSSFile(Request $request)
    {
        $input = $request->only('id');
        $validationCondition = [
            'id' => 'required|array',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::deleteOSSFile($input);
    }

    /**
     * @description: 上传到本地
     * @author Neo
     * @param {Request} $request
     * @return {*}
     */
    public function uploadLocal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'select_file'  => 'required|mimes:jpg,jpeg,png,webp,gif,mp3,mp4,mkv,wmv,xlsx,xls,doc,docx,txt,sql,zip,rar,7z,tar,gz,iso,pdf|max:1024000', // 最大1000MB，约1G
            'platform' => 'required|integer', // 类型：1-后台CMS，2-微信小程序，3-H5，4-安卓，5-IOS，6-其他
        ]);

        if ($validator->fails()) {
            $error['msg'] = $validator->errors()->first();
            return self::output(200, 'error', '参数有误', $error);
        }

        return MediaService::uploadLocal($request);
    }

    /**
     * @description: 单个删除本地媒体
     * @author Neo
     * @param {Request} $request
     * @return {*}
     */
    public static function deleteLocal(Request $request)
    {
        $input = $request->only('id');
        $validationCondition = [
            'id' => 'required|array',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaService::deleteLocal($input);
    }

    /**
     * @description: 后续补上上传COS
     * @author Neo
     * @param {Request} $request
     * @return {*}
     */
    public function uploadCOS(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'select_file'  => 'required|mimes:jpg,jpeg,png,gif,mp3,mp4,mkv,wmv,xlsx,xls,doc,docx,txt,sql,zip,rar,7z,tar,gz,iso|max:1024000', // 最大1000MB，约1G
        ]);

        if ($validator->fails()) {
            $error['msg'] = $validator->errors()->first();
            return self::output(200, 'error', '参数有误', $error);
        }

        $data = [];

        return self::output(200, 'success', '上传成功', $data);
    }

    // 后续完成laravel oss私有文件访问和删除

    public function index(Request $request)
    {
        // 创建目录，OK
        // $create = $disk->makeDirectory('testing2');

        // 检查文件是否存在
        // $exists = $disk->has('testing/test1.txt');
        // return $exists;

        // 获取文件修改时间
        // $time = $disk->lastModified('file1.jpg');
        // $time = $disk->getTimestamp('file1.jpg');

        // // 拷贝文件
        // $disk->copy('old/file1.jpg', 'new/file1.jpg');

        // 移动文件也可改名
        // $disk->move('testing/test1.txt', 'testing/test2.txt');


        // 获取文件内容
        // $contents = $disk->read('https:/hosource.oss-cn-shenzhen.aliyuncs.com/testing/test2.txt');


        //切换其他bucket
        // $exists = $disk->bucket('test')->has('testing/test1.txt');

        // //下载
        // $disk->download('testing/test2.txt');
        // return ;

        //指定名字
        // $disk->download('testing/test1.txt','file_name');

        // //文件大小
        // $size = $disk->size('testing/test2.txt');
        // return $size;

        // //删除指定文件
        // $disk->delete('testing/test1.txt');
        // //删除多个文件
        // $disk->delete(['testing/test1.txt', 'file2.jpg']);

        // //创建目录
        // $disk->makeDirectory('/your/oss/path/');
        // //删除目录
        // $disk->deleteDirectory('/your/oss/path/');
    }
}
