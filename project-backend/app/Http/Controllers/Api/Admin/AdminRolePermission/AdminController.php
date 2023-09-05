<?php

namespace App\Http\Controllers\Api\Admin\AdminRolePermission;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\AdminRolePermission\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends BaseController
{
    /**
     * @description: 列表
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function list(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'page' => 'nullable|integer', // 分页号码
            'limit' => 'nullable|integer', // 每页多少记录
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminService::list($request);
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
            'id' => 'required|integer',
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminService::details($request);
    }

    /**
     * @description: 注册
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function register(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'username' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'user_roles' => 'nullable|array', // 用户的角色集合
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminService::register($request);
    }

    /**
     * @description: 后台用户列表，没有修改名字和密码功能，只能启用/停用，删除功能
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function edit(Request $request): JsonResponse
    {
        $validatorResult = $this->validatorStopOnFirst($request, [
            'id' => 'required|integer',
            'status' => 'nullable|in:1,2',
            'user_roles' => 'nullable|array', // 用户的角色集合
        ]);
        if (!is_null($validatorResult)) {
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $validatorResult, []);
        }

        return AdminService::edit($request);
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

        return AdminService::delete($request);
    }
}
