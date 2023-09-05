<?php

namespace App\Http\Controllers\Api\Admin\Page;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\Page\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends BaseController
{
    /**
     * 页面列表
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

        return PageService::list($input);
    }

    /**
     * 页面详情
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
        return PageService::details($input);
    }

    /**
     * 创建页面
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

        return PageService::create($input);
    }

    /**
     * 编辑页面
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function edit(Request $request)
    {
        // 验证参数
        $input = $request->only('id', 'title', 'image', 'content', 'url_tag', 'json', 'status', 'remarks');
        $validationCondition = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'image' => 'nullable|integer',
            'content' => 'nullable|string',
            'url_tag' => 'nullable|string',
            'json' => 'nullable|string',
            'status' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return PageService::edit($input);
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

        return PageService::delete($request);
    }
}
