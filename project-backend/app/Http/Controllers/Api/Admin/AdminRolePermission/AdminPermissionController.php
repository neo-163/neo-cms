<?php

namespace App\Http\Controllers\Api\Admin\AdminRolePermission;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\AdminRolePermission\AdminPermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPermissionController extends BaseController
{
    /**
     * @description: 列表
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function list(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'page' => 'nullable|integer', // 分页号码
            'limit' => 'nullable|integer', // 每页多少记录
            'title' => 'nullable|string', // 可查询标题
            'description' => 'nullable|integer', // 可查询描述
            'status' => 'nullable|integer|in:1,2', // 可查询状态
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminPermissionService::list($request);
    }

    /**
     * @description: 规则权限的分级选项列表
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function selects(Request $request)
    // : JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'id' => 'required|integer',
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminPermissionService::selects($request);
    }

    /**
     * @description: 详情
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function details(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'id' => 'required|integer', // 查询的id
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminPermissionService::details($request);
    }

    /**
     * @description: 保存：新增+编辑
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function store(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'id' => 'nullable|integer', // 有id代表编辑
            'title' => 'required|string', // 标题，必填
            'route' => 'required|string', // 标题，必填
            'description' => 'nullable|integer', // 描述，选填
            'parent' => 'nullable|integer', // 父级，默认0
            'sort' => 'required|integer', // 必填，避免重复
            'status' => 'nullable|in:1,2', // 限制输入范围：数字1，2
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminPermissionService::store($request);
    }

    /**
     * @description: 删除
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'id' => 'required|array', // 可批量删除
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminPermissionService::delete($request);
    }
}
