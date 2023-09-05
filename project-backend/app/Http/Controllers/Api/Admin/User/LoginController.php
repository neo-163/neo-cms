<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\User\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    /**
     * @description: 注册
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function register(Request $request)
    {
        // 验证参数
        $input = $request->only('username', 'email', 'password', 'password_confirmation');
        $validationCondition = [
            'username' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return LoginService::register($input);
    }

    /**
     * @description: 登录
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function login(Request $request)
    {
        // 验证参数
        $input = $request->only('username', 'password', 'remember_me');
        $validationCondition = [
            'username' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'nullable|boolean',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return LoginService::login($input);
    }

    /**
     * @description: 登出
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function logout(Request $request)
    {
        return LoginService::logout($request);
    }
}
