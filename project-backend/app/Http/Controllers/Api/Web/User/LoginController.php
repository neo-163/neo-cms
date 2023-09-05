<?php

namespace App\Http\Controllers\Api\Web\User;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\PromptCode\SystemCode;
use App\Services\Api\Web\User\LoginService;

class LoginController extends BaseController
{
    /**
     * 注册逻辑：名字不能有@，和邮箱避免重复；同步和电话号码对比，避免重复；用户名/邮箱/电话号码，可以直接登录
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function register(Request $request)
    {
        // 验证参数
        $input = $request->only('username', 'description', 'email', 'area_code', 'phone', 'code', 'password', 'password_confirmation');
        $validationCondition = [
            'username' => 'required|string',
            'description' => 'nullable|string',
            'email' => 'required|string|email',
            'area_code' => 'nullable|integer',
            'phone' => 'nullable|integer',
            'code' => 'required|string',
            'password' => 'required|string|confirmed|min:6|max:100',
            'password_confirmation' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return LoginService::register($input);
    }

    /**
     * 登录，加入多次失败的验证
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function login(Request $request)
    {
        // 验证参数
        $input = $request->only('username', 'password', 'remember_me');
        $validationCondition = [
            'username' => 'required|string', // 日后可以email或者手机号码登录
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        $user = $request->user();
        return LoginService::login($input, $user);
    }

    /**
     * 登出
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->each(function ($token) {
                $token->delete();
            });

            return self::output(SystemCode::CODE_SUCCESS, 'success', '登出成功', []);
        } catch (\Exception $e) {
            return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_ERROR, $e->getMessage(), []);
        }
    }

    /**
     * 生成邀请码，废弃
     *
     * @return void
     */
    public static function createInvitecode()
    {
        // 生成字母和数字组成的6位字符串
        $str = range('A', 'Z');

        // 去除大写的O，以防止与0混淆 
        unset($str[array_search('O', $str)]);
        $arr = array_merge(range(0, 9), $str);
        shuffle($arr);

        $invitecode = '';
        $arr_len = count($arr);
        for ($i = 0; $i < 8; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $invitecode .= $arr[$rand];
        }

        return $invitecode;
    }
}
