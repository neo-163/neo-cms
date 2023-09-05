<?php

namespace App\Http\Controllers\Api\Admin\Article;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\Article\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends BaseController
{
    /**
     * @description: 页面列表
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
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

        return ArticleService::list($input);
    }

    /**
     * @description: 文章详情
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
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
        return ArticleService::details($input);
    }

    /**
     * @description: 创建页面
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function create(Request $request)
    {
        // 验证参数
        $input = $request->only('title', 'image', 'content', 'json', 'status', 'remarks');
        $validationCondition = [
            'title' => 'required|string',
            'image' => 'nullable|integer',
            'content' => 'nullable|string',
            'json' => 'nullable|string',
            'status' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return ArticleService::create($input);
    }

    /**
     * @description: 编辑页面
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function edit(Request $request)
    {
        // 验证参数
        $input = $request->only('id', 'title', 'image', 'content', 'json', 'status', 'remarks');
        $validationCondition = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'image' => 'nullable|integer',
            'content' => 'nullable|string',
            'json' => 'nullable|string',
            'status' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return ArticleService::edit($input);
    }

    /**
     * @description: 删除
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
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

        return ArticleService::delete($request);
    }
}
