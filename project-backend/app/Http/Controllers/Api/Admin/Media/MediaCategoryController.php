<?php

namespace App\Http\Controllers\Api\Admin\Media;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\Media\MediaCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaCategoryController extends BaseController
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

        return MediaCategoryService::list($input);
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
        return MediaCategoryService::details($input);
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
        $input = $request->only('title', 'description');
        $validationCondition = [
            'title' => 'required|string',
            'description' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        $input['adminUser'] = $request['adminUser'];

        return MediaCategoryService::create($input);
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
        $input = $request->only('id', 'title', 'description', 'sort');
        $validationCondition = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        $input['adminUser'] = $request['adminUser'];

        return MediaCategoryService::edit($input);
    }

    /**
     * 草稿
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function change(Request $request)
    {
        // 验证参数
        $input = $request->only('json');
        $validationCondition = [
            'json' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return MediaCategoryService::change($input);
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

        return MediaCategoryService::delete($request);
    }
}
